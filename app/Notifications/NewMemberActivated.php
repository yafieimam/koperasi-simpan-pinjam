<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewMemberActivated extends Notification
{
    use Queueable;
    public $member;
    public $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($member, $subject = null)
    {
        $this->member = $member;
        if(is_null($subject)){
            $this->subject = "Status Anggota Telah Aktif";
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
            ->subject($this->subject)
            ->markdown('mail.member.new', ['member'=> $this->member]);
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
            'url'=>'member',
            'content'=> [
                'title'=>'Status Anggota Telah Aktif',
                'description'=> 'Status Anggota Anda Telah Aktif',
                'object'=> $this->member,
                'object_type'=> 'App\Member'
            ],
            'icon'=> 'fa-user',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->setSubject('Status anggota telah aktif')
            ->setBody("Cek sekarang")
            ->setData('type','members')
            ->setData('id', $this->member->id)
            ->setData('user_id', $this->member->user_id);
//            ->setButton(
//                OneSignalButton::create('link-1')
//                    ->text('Click here')
//                    ->icon('https://upload.wikimedia.org/wikipedia/commons/4/4f/Laravel_logo.png')
//            );
    }

}
