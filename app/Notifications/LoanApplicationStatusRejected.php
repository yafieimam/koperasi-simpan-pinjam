<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class LoanApplicationStatusRejected extends Notification
{
    use Queueable;
    public $loanApplication;
    /**
     * Create a new notification instance.
     *
     * LoanApplicationStatusUpdated constructor.
     * @param $loanApplication
     */
    public function __construct($loanApplication)
    {
        $this->loanApplication = $loanApplication;
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
        $status = $this->loanApplication->status;
        if($this->loanApplication->status == 'ditolak'){
            $status = "Ditolak";
        }else if($this->loanApplication->status == 'dibatalkan'){
            $status = "Dibatalkan";
        }
        return (new MailMessage)
            ->subject('Pinjaman No. ' . $this->loanApplication->loan_number . ' ' . $status)
            ->markdown('mail.loan.status-updated',['loanApplication'=> $this->loanApplication, 'status' => $status]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $status = $this->loanApplication->status;
        if($this->loanApplication->status == 'ditolak'){
            $status = "ditolak";
        }else if($this->loanApplication->status == 'dibatalkan'){
            $status = "dibatalkan";
        }
        return [
            'url'=>'loan',
            'content'=> [
                'title'=>  'Pinjaman No. ' . $this->loanApplication->loan_number . ' ' . $status,
                'description'=> 'Perubahan status pengajuan pinjaman no. ' . $this->loanApplication->loan_number . ' ' . $status,
                'object'=> $this->loanApplication,
                'object_type'=> 'App\TsLoans'
            ],
            'icon'=> 'fa-handshake-o',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        $status = $this->loanApplication->status;
        if($this->loanApplication->status == 'ditolak'){
            $status = "ditolak";
        }else if($this->loanApplication->status == 'dibatalkan'){
            $status = "dibatalkan";
        }
        return OneSignalMessage::create()
            ->setSubject("Pinjaman No. " . $this->loanApplication->loan_number . " " . $status)
            ->setBody("Cek Sekarang")
            ->setData('type','loan')
            ->setData('id', $this->loanApplication->id)
            ->setData('user_id', $this->loanApplication->member->user_id);
    }
}
