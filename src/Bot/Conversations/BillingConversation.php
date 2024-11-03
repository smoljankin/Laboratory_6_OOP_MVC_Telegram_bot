<?php

namespace App\Bot\Conversations;

use App\Models\OrderModel;
use App\Models\ProductModel;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class BillingConversation extends Conversation {
    public function start(Nutgram $bot)
    {
        $bot->sendMessage('Введіть ваше повне імя');
        $this->next('userNameStep');
    }

    public function userNameStep(Nutgram $bot)
    {
        $name = $bot->message()->text;
        $bot->setUserData('username', $name);

        $bot->sendMessage('Enter your email');
        $this->setSkipHandlers(true)->next('emailStep');
    }

    public function emailStep(Nutgram $bot)
    {
        $email = $bot->message()->text;
        $bot->setUserData('email', $email);

        $bot->sendMessage('Enter your address');
        $this->setSkipHandlers(true)->next('addressStep');
    }

    public function addressStep(Nutgram $bot)
    {
        $address = $bot->message()->text;
        $bot->setUserData('address', $address);

        $bot->sendMessage('Enter your phone number');
        $this->setSkipHandlers(true)->next('phoneStep');
    }

    public function phoneStep(Nutgram $bot)
    {
        $phone = $bot->message()->text;
        $bot->setUserData('phone', $phone);

        $name = $bot->getUserData('username');
        $email = $bot->getUserData('email');
        $address = $bot->getUserData('address');

        $text = sprintf('You entered such billing data(%s, %s, %s, %s). Is it correct?', $name, $email, $address, $phone);

        $replyMarkup = InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make('Так', callback_data: 'confirmStep'),
            InlineKeyboardButton::make('Змінити дані', callback_data: 'billing_start'),
        );

        $bot->sendMessage($text, [
            'reply_markup' => $replyMarkup
        ]);
        $this->setSkipHandlers(true)->next('confirmStep');
    }

    public function confirmStep(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) {
            $this->phoneStep($bot);
            return;
        }

        $confirm = $bot->callbackQuery()->data;
        if ($confirm !== 'confirmStep') {
            $this->phoneStep($bot);
        }

        $this->createOrder();

        $bot->sendMessage('Вітаю. Ви створили замовлення. Переглянути його можете в списку ваших замовлень.');
        $bot->answerCallbackQuery();
        $this->end();
    }

    private function createOrder() {
        $email = $this->bot->getUserData('email');
        $name = $this->bot->getUserData('username');
        $address = $this->bot->getUserData('address');
        $phone = $this->bot->getUserData('phone');
        $productId = $this->bot->getUserData('order_product_id');
        $productNum = $this->bot->getUserData('order_product_num');

        $userId = $this->bot->chat()->id;

        $orderModel = $this->bot->getContainer()->get(OrderModel::class);
        $orderModel->createOrder($email, $name, $address, $phone, $productId, $productNum, $userId);

        $this->bot->deleteUserData('email');
        $this->bot->deleteUserData('username');
        $this->bot->deleteUserData('address');
        $this->bot->deleteUserData('phone');
        $this->bot->deleteUserData('order_product_id');
        $this->bot->deleteUserData('order_product_num');
    }
}
