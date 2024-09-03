<?php

namespace App\Notifications;

use App\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class PencairanSimpananApplicationStatusUpdated extends Notification
{
    use Queueable;
    public $depositApplication;
    /**
     * Create a new notification instance.
     *
     * LoanApplicationStatusUpdated constructor.
     * @param $loanApplication
     */
    public function __construct($depositApplication)
    {
        $this->depositApplication = $depositApplication;
    }

    /**
    * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database',OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $position = $position = Position::find($this->depositApplication->status_by);
        return (new MailMessage)
            ->subject('Pencairan Simpanan Anda Disetujui ' . $position->name)
            ->markdown('mail.deposit.status-updated',['depositApplication'=> $this->depositApplication, 'status' => 'disetujui']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $position = $position = Position::find($this->depositApplication->status_by);
        return [
            'url'=>'pencairan-deposit-member',
            'content'=> [
                'title'=> 'Pencairan simpanan Anda Disetujui ' . $position->name,
                'description'=> 'Perubahan status pencairan simpanan anda disetujui oleh ' . $position->name,
                'object'=> $this->depositApplication,
                'object_type'=> 'App\PencairanSimpanan'
            ],
            'icon'=> 'fa-handshake-o',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        $position = $position = Position::find($this->depositApplication->status_by);
        return OneSignalMessage::create()
            ->setSubject('Pencairan Simpanan Anda Disetujui ' . $position->name)
            ->setBody("Cek Sekarang")
            ->setData('type','pencairan-simpanan')
            ->setData('id', $this->depositApplication->id)
            ->setData('user_id', $this->depositApplication->member->user_id);
    }
}
