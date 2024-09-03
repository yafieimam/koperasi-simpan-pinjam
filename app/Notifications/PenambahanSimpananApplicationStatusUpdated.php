<?php

namespace App\Notifications;

use App\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class PenambahanSimpananApplicationStatusUpdated extends Notification
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
        return (new MailMessage)
            ->subject('Penambahan Simpanan Anda Disetujui Supervisor Koperasi')
            ->markdown('mail.deposit.add-status-updated',['depositApplication'=> $this->depositApplication, 'status' => 'disetujui']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'url'=>'add-deposit-member',
            'content'=> [
                'title'=> 'Penambahan simpanan Anda Disetujui Supervisor Koperasi',
                'description'=> 'Perubahan status penambahan simpanan anda disetujui oleh Supervisor Koperasi',
                'object'=> $this->depositApplication,
                'object_type'=> 'App\TsDeposits'
            ],
            'icon'=> 'fa-handshake-o',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        $position = $position = Position::find($this->depositApplication->status_by);
        return OneSignalMessage::create()
            ->setSubject('Penambahan Simpanan Anda Disetujui Supervisor Koperasi')
            ->setBody("Cek Sekarang")
            ->setData('type','add-deposit')
            ->setData('id', $this->depositApplication->id)
            ->setData('user_id', $this->depositApplication->member->user_id);
    }
}
