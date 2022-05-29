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
        $text .= '<b>' . __('Salam') . '</b>' . PHP_EOL;
        $text .= __('Oyuna Başlamaq üçün <Oyna> Düyməsini klikləyin və sonra oynamaq üçün söhbət seçin ., ['{USAGE}' => '<b>\'@' . $this->getTelegram()->getBotUsername() . ' ...\'</b>', '{BUTTON}' => '<b>\'' . __('Oyna') . '\'</b>']);

        $data = [
            'chat_id'                  => $chat_id,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
            'reply_markup'             => new InlineKeyboard(
                [
                    new InlineKeyboardButton(
                        [
                            'text'                => __('Oyna') . ' ' . Emoji::gameDie(),
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
