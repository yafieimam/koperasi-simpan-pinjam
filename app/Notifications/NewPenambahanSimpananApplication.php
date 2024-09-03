<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class NewPenambahanSimpananApplication extends Notification
{
	use Queueable;
	public $depositApplication;
	public $subject;
    public $via;

    /**
     * NewresignApplication constructor.
     * @param $resignApplication
     * @param array $via
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
			->subject('Terdapat Pengajuan Penambahan Simpanan Baru')
			->markdown('mail.deposit.add-deposit',['depositApplication'=> $this->depositApplication]);
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
			'url'=>'add-deposit',
			'content'=> [
				'title' => 'Terdapat Penambahan Simpanan Baru',
				'description'=> $this->depositApplication->member->full_name.' mengajukan penambahan simpanan',
				'object'=> $this->depositApplication,
				'object_type'=> 'App\TsDeposits'
			],
			'icon'=> 'fa-handshake-o',
			'icon-color'=> 'red'
		];
	}

	public function toOneSignal($notifiable)
	{
		return OneSignalMessage::create()
			->setSubject("Terdapat Penambahan Simpanan Baru")
			->setBody("Cek sekarang")
			->setData('type','add-deposit')
			->setData('id', $this->depositApplication->id)
			->setData('user_id', $this->depositApplication->member['id']);
	}
}
