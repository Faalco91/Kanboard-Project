<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ProjectInvitation extends Notification
{
    use Queueable;

    protected $project;
    protected $role;

    public function __construct(Project $project, string $role)
    {
        $this->project = $project;
        $this->role = $role;
        Log::info('ProjectInvitation notification créée', [
            'project' => $project->id,
            'role' => $role
        ]);
    }

    public function via($notifiable): array
    {
        Log::info('ProjectInvitation via appelé', [
            'notifiable' => $notifiable->email
        ]);
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        Log::info('Préparation de l\'email d\'invitation', [
            'to' => $notifiable->email,
            'project' => $this->project->name
        ]);

        $url = URL::signedRoute('project.members.show-invitation', [
            'project' => $this->project->id
        ]);

        return (new MailMessage)
            ->subject('Invitation à rejoindre le projet ' . $this->project->name)
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Vous avez été invité(e) à rejoindre le projet "' . $this->project->name . '" en tant que ' . $this->role . '.')
            ->line('Cliquez sur le bouton ci-dessous pour voir et gérer l\'invitation.')
            ->action('Voir l\'invitation', $url)
            ->line('Si vous n\'attendiez pas cette invitation, vous pouvez l\'ignorer ou la rejeter une fois connecté(e).')
            ->salutation('Cordialement,');
    }
} 