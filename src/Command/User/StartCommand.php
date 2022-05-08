<?php
/**
 *  Rocks Gami Bot - Telegram Bot (@RocksGameBot)
 *
 * (c) 2022 rocks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bot\Command\User;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Spatie\Emoji\Emoji;

/**
 * Start command...
 *
 * @noinspection PhpUndefinedClassInspection
 */
class StartCommand extends UserCommand
{
    /**
     * @return mixed
     *
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getUpdate()->getMessage();
        $edited_message = $this->getUpdate()->getEditedMessage();
        $callback_query = $this->getUpdate()->getCallbackQuery();

        if ($edited_message) {
            $message = $edited_message;
        }

        $chat_id = null;
        $data_query = null;

        if ($message) {
            if (!$message->getChat()->isPrivateChat()) {
                return Request::emptyResponse();
            }

            $chat_id = $message->getChat()->getId();
        } elseif ($callback_query) {
            $chat_id = $callback_query->getMessage()->getChat()->getId();

            $data_query = [];
            $data_query['callback_query_id'] = $callback_query->getId();
        }

        $text = Emoji::wavingHand() . ' ';
        $text .= '<b>' . __('**Salam, Rocks Games Bot'a xoş gəldin!**') . '</b>' . PHP_EOL;
        $text .= __(✦ 𝙾𝚢𝚞𝚗𝚊 𝙱𝚊𝚜̧𝚕𝚊𝚖𝚊𝚚 𝚞̈𝚌̧𝚞̈𝚗 𝐎𝐲𝐧𝐚 𝙳𝚞𝚢𝚖𝚎𝚜𝚒𝚗𝚒 𝚔𝚒𝚕𝚒𝚔𝚕𝚎𝚢𝚒𝚗 𝚟𝚎 𝚜𝚘𝚗𝚛𝚊 𝚘𝚢𝚗𝚊𝚖𝚊𝚚 𝚞̈𝚌̧𝚞̈𝚗 𝚜𝚘̈𝚑𝚋𝚎𝚝 𝚜𝚎𝚌̧𝚒𝚗., ['{USAGE}' => '<b>\'@' . $this->getTelegram()->getBotUsername() . ' ...\'</b>', '{BUTTON}' => '<b>\'' . __('𝐎𝐲𝐧𝐚') . '\'</b>']);

        $data = [
            'chat_id'                  => $chat_id,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
            'reply_markup'             => new InlineKeyboard(
                [
                    new InlineKeyboardButton(
                        [
                            'text'                => __('𝐎𝐲𝐧𝐚') . ' ' . Emoji::gameDie(),
                            'switch_inline_query' => Emoji::gameDie(),
                        ]
                    ),
                ]
            ),
        ];

        if ($message) {
            return Request::sendMessage($data);
        } elseif ($callback_query) {
            $data['message_id'] = $callback_query->getMessage()->getMessageId();
            $result = Request::editMessageText($data);
            Request::answerCallbackQuery($data_query);

            return $result;
        }

        return Request::emptyResponse();
    }
}
