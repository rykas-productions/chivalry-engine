<?php

namespace AG;

use Exception;

/**
 * Message to Discord Chanel
 *
 * Uses simple webhook api
 * See https://github.com/agorlov/discordmsg for details.
 *
 * Official doc about webhook
 * @link https://discordapp.com/developers/docs/resources/webhook#execute-webhook
 *
 * Example:
 *
 * ```php
 * (new \AG\DiscordMsg('Hello'))->send();
 * ```
 *
 * DiscordMsg is immutable object.
 *
 * @package AG
 * @author Alexandr Gorlov <a.gorlov@gmail.com>
 */
final class DiscordMsg implements Msg
{
    private $msg;
    private $url;
    private $username;
    private $avatar;

    /**
     * DiscordMsg constructor.
     *
     * To Test Join https://discord.gg/jB5FsPf
     *
     * How to create own webhook see at https://github.com/agorlov/discordmsg
     *
     * @param string $msg text messae
     * @param string $url Discord Webhook url (default is sandboxc channel, put yours chanel here)
     * @param string|null $username
     * @param string|null $avatar
     */
    public function __construct(
        string $msg,
        string $url = null,
        string $username = null,
        string $avatar = null
    )
    {
        //MasterGeneral156 left his bot auth'd in here because he is the dumb.
        //He has since invalidated this bot's token.
        //Other games could send announcements to the Chivalry is Dead discord,
        //which isn't a good thing. My bad.
        
        //Make sure to input the proper webhook url below. I'm not going to help you
        //if you're still using the old, invalid URL.
        $this->msg = $msg;
        $this->url = $url ??
            'PASTE YOUR WEBHOOK URL HERE';
        $this->username = $username ?? 'Bot Name';
        $this->avatar = $avatar ??
            'Bot Image, URL';
    }

    /**
     * Sends message
     *
     * @return void
     * @throws \Exception
     */
    public function send(): void
    {
        $curl = curl_init();
        //timeouts - 5 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 5); // 5 seconds
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); // 5 seconds

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            'content' => $this->msg,
            'username' => $this->username,
            'avatar_url' => $this->avatar,
        ]));

        $output = json_decode(
            curl_exec($curl),
            true
        );

        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 204) {
            curl_close($curl);
            throw new Exception("Something went wrong to send a discord message: " . $output['message']);
        }

        curl_close($curl);
    }
}