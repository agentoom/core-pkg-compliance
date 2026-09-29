<?php

namespace Agentoom\Compliance\Services;

use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Core\Models\AgentConversationMessage;
use Illuminate\Support\Collection;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\FinishReason;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Step;
use Laravel\Ai\Responses\StreamedAgentResponse;
use Laravel\Ai\Responses\TextResponse;
use Laravel\Ai\Streaming\Events\ReasoningDelta;
use Laravel\Ai\Streaming\Events\StreamEnd;
use Laravel\Ai\Streaming\Events\TextDelta;
use Laravel\Ai\Streaming\Events\ToolCall as ToolCallEvent;
use Laravel\Ai\Streaming\Events\ToolResult as ToolResultEvent;

class AgentAuditor
{
    public static function trace(Agent $agent, TextResponse $response, ?AgentConversationMessage $currentMessage = null, ?int $executionTimeMs = null, ?string $traceableType = null, ?int $traceableId = null): ?AiAgentTrace
    {
        $steps = $response->steps;

        if ($steps->isEmpty() && $response instanceof StreamedAgentResponse) {
            $steps = static::reconstructStepsFromEvents($response->events, $response->meta);
        }

        if ($steps->isEmpty() && ($response instanceof TextResponse)) {
            // Synthesize a single step if steps are missing (fallback)
            if (filled($response->text) || $response->toolCalls->isNotEmpty()) {
                $steps = collect([
                    new Step(
                        $response->text,
                        $response->toolCalls->all(),
                        $response->toolResults->all(),
                        FinishReason::Stop,
                        $response->usage,
                        $response->meta
                    ),
                ]);
            }
        }

        $usage = $response->usage;
        $conversationId = null;

        if (method_exists($agent, 'currentConversation')) {
            $conversationId = $agent->currentConversation();
        }

        if ($response instanceof AgentResponse && ! $conversationId) {
            $conversationId = $response->conversationId;
        }

        $agentId = null;
        if (method_exists($agent, 'getAgentModel')) {
            $agentId = $agent->getAgentModel()->id;
        }

        if (! $agentId) {
            return null;
        }

        // Map out the inner monologue and tool traces
        $stepsPayload = collect($steps)->map(function ($step, $index) {
            $text = $step->text;
            $thinking = null;

            // Try to extract thinking from tags (common in reasoning models like DeepSeek)
            if (preg_match('/<(?:think|thought)>(.*?)<\/(?:think|thought)>/s', $text, $matches)) {
                $thinking = trim($matches[1]);
                $text = trim(str_replace($matches[0], '', $text));
            }

            return [
                'step_number' => $index + 1,
                'text' => $text,
                'thinking' => $thinking,
                'tool_calls' => collect($step->toolCalls)->values()->map(fn ($tc) => [
                    'name' => $tc->name,
                    'arguments' => $tc->arguments,
                ])->toArray(),
                'tool_results' => collect($step->toolResults)->values()->map(fn ($tr) => [
                    'name' => $tr->name,
                    'result' => $tr->result,
                ])->toArray(),
                'raw_blocks' => null, // Step does not have providerContentBlocks in this version
            ];
        })->toArray();

        $messageId = $currentMessage?->id;

        if (! $messageId && $conversationId) {
            $messageId = AgentConversationMessage::where('conversation_id', $conversationId)
                ->where('role', 'assistant')
                ->latest()
                ->first()?->id;
        }

        // Pulling IDs directly out of the active runtime state of the SDK agent
        return AiAgentTrace::create([
            'conversation_id' => $conversationId,
            'agent_id' => $agentId,
            'ai_message_id' => $messageId,
            'traceable_type' => $traceableType,
            'traceable_id' => $traceableId,
            'model' => $response->meta->model ?? 'unknown',
            'total_steps' => count($steps),
            'prompt_tokens' => $usage->promptTokens ?? 0,
            'completion_tokens' => $usage->completionTokens ?? 0,
            'total_tokens' => ($usage->promptTokens ?? 0) + ($usage->completionTokens ?? 0),
            'execution_time_ms' => $executionTimeMs,
            'steps_json' => $stepsPayload,
        ]);
    }

    /**
     * Reconstruct steps from a stream of events.
     * A new step starts whenever a new piece of content or tool call follows a tool result.
     */
    private static function reconstructStepsFromEvents(Collection $events, ?Meta $meta): Collection
    {
        $steps = collect();
        $currentEvents = collect();
        $hasToolResult = false;

        foreach ($events as $event) {
            // If we already have a tool result in the current set of events,
            // and we encounter a new content delta or tool call, it means a new iteration started.
            if ($hasToolResult && ($event instanceof TextDelta || $event instanceof ToolCallEvent || $event instanceof ReasoningDelta)) {
                $steps->push(static::createStepFromEvents($currentEvents, $meta));
                $currentEvents = collect();
                $hasToolResult = false;
            }

            $currentEvents->push($event);

            if ($event instanceof ToolResultEvent) {
                $hasToolResult = true;
            }
        }

        if ($currentEvents->isNotEmpty()) {
            $steps->push(static::createStepFromEvents($currentEvents, $meta));
        }

        return $steps;
    }

    /**
     * Create a single step from a collection of events.
     */
    private static function createStepFromEvents(Collection $events, ?Meta $meta): Step
    {
        $text = TextDelta::combine($events);
        $reasoning = $events->whereInstanceOf(ReasoningDelta::class)->map->delta->join('');

        if (filled($reasoning)) {
            // Wrap reasoning in tags so existing mapping logic picks it up correctly
            $text = "<think>{$reasoning}</think>\n".$text;
        }

        $toolCalls = $events->whereInstanceOf(ToolCallEvent::class)->map->toolCall->all();
        $toolResults = $events->whereInstanceOf(ToolResultEvent::class)->map->toolResult->all();

        $usage = StreamEnd::combineUsage($events);
        $finishReason = $events->whereInstanceOf(StreamEnd::class)->first()?->reason ?? FinishReason::Stop;

        if (is_string($finishReason)) {
            $finishReason = FinishReason::tryFrom($finishReason) ?? FinishReason::Stop;
        }

        return new Step(
            $text,
            $toolCalls,
            $toolResults,
            $finishReason,
            $usage,
            $meta
        );
    }
}
