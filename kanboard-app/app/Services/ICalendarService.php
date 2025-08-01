<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;

class ICalendarService
{
    public function generateICalendar(Project $project): string
    {
        $tasks = $project->tasks()->whereNotNull('due_date')->get();
        
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Kanboard//Laravel//FR\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        
        foreach ($tasks as $task) {
            $ical .= $this->generateTaskEvent($task);
        }
        
        $ical .= "END:VCALENDAR\r\n";
        
        return $ical;
    }
    
    private function generateTaskEvent(Task $task): string
    {
        $dueDate = Carbon::parse($task->due_date);
        $created = Carbon::parse($task->created_at);
        $updated = Carbon::parse($task->updated_at);
        
        $event = "BEGIN:VEVENT\r\n";
        $event .= "UID:" . $task->id . "@kanboard.local\r\n";
        $event .= "DTSTAMP:" . $created->format('Ymd\THis\Z') . "\r\n";
        $event .= "DTSTART:" . $dueDate->format('Ymd\THis\Z') . "\r\n";
        $event .= "DTEND:" . $dueDate->copy()->addHour()->format('Ymd\THis\Z') . "\r\n";
        $event .= "SUMMARY:" . $this->escapeText($task->title) . "\r\n";
        $event .= "DESCRIPTION:" . $this->escapeText($task->description ?? '') . "\r\n";
        
        // Gérer le statut selon la colonne
        $status = $this->getStatusFromColumn($task->column);
        $event .= "STATUS:" . $status . "\r\n";
        
        $event .= "PRIORITY:" . $this->getPriority($task->priority ?? null) . "\r\n";
        $event .= "LAST-MODIFIED:" . $updated->format('Ymd\THis\Z') . "\r\n";
        $event .= "END:VEVENT\r\n";
        
        return $event;
    }
    
    private function escapeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r", "\n"], "\\n", $text);
        $text = str_replace(["\t"], "\\t", $text);
        $text = str_replace(["\""], "\\\"", $text);
        $text = str_replace([";"], "\\;", $text);
        $text = str_replace([","], "\\,", $text);
        return $text;
    }
    
    private function getPriority(?string $priority): int
    {
        return match($priority) {
            'high' => 1,
            'medium' => 5,
            'low' => 9,
            null => 5,
            default => 5
        };
    }
    
    private function getStatusFromColumn(string $column): string
    {
        return match(strtolower($column)) {
            'done', 'fait', 'terminé' => 'COMPLETED',
            'in progress', 'en cours' => 'IN-PROCESS',
            'cancelled', 'annulé' => 'CANCELLED',
            default => 'NEEDS-ACTION'
        };
    }
    
    public function getICalHeaders(string $filename = 'calendar.ics'): array
    {
        return [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
    }
    
    public function generateFilename(Project $project): string
    {
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $project->name);
        return strtolower($safeName) . '_calendar.ics';
    }
}