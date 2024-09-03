<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class WaitResignApplication extends Notification
{
	use Queueable;
	public $resignApplication;
	public $subject;
    public $via;

    /**
     * NewresignApplication constructor.
     * @param $resignApplication
     * @param array $via
     */
	public function __construct($resignApplication)
	{
		$this->resignApplication = $resignApplication;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail','database', OneSignalChannel::class];
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
			->subject('Terdapat Pengunduran Diri Menunggu Persetujuan Anda')
			->markdown('mail.loan.wait-resign',['resignApplication'=> $this->resignApplication]);
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
			'url'=>'resign-wait',
			'content'=> [
				'title' => 'Terdapat Pengunduran Diri Menunggu Persetujuan Anda',
				'description'=> 'Pengunduran diri ' . $this->resignApplication->member->full_name . ' menunggu persetujuan Anda',
				'object'=> $this->resignApplication,
				'object_type'=> 'App\Resign'
			],
			'icon'=> 'fa-handshake-o',
			'icon-color'=> 'red'
		];
	}

	public function toOneSignal($notifiable)
	{
		return OneSignalMessage::create()
			->setSubject("Terdapat Pengunduran Diri Menunggu Persetujuan Anda")
			->setBody("Cek sekarang")
			->setData('type','resign')
			->setData('id', $this->resignApplication->id)
			->setData('user_id', $this->resignApplication->member['id']);
	}
}
