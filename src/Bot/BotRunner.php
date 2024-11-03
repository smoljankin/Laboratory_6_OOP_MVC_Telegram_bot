<?php

namespace App\Bot;

use App\Bot\Commands\StartCommand;
use App\Bot\Conversations\BillingConversation;
use App\Bot\Conversations\FirstConversation;
use App\Core\DbConnection;
use App\Models\ProductModel;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ForceReply;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class BotRunner {
    private $bot;
    private $config = [];
    private $models = [];

    public function __construct($telegramToken, $configFile) {
        $this->bot = new Nutgram($telegramToken);
        $this->bot->setRunningMode(Polling::class);

        $this->config = require_once($configFile);
        $dbConnection = new DbConnection($this->config['dbFile']);
        $this->models = $this->prepareModels($this->config['models'], $dbConnection);
    }

    public function run() {
        $this->configure();    
        $this->bot->run();
    }

    private function configure() {
        $this->bot->registerCommand(StartCommand::class);

        $this->bot->onText('([0-9]+)', function (Nutgram $bot, $n) {
            $productId = $bot->getUserData('order_product_id');
            if (empty($productId)) {
                return;
            }

            $bot->setUserData('order_product_num', $n);

            $productModel = $bot->getContainer()->get(ProductModel::class);
            $productInfo = $productModel->getFullInfoById($productId);
            if (empty($productInfo)) {
                return;
            }

            $name = $productInfo['name'];
            $total = $productInfo['price'] * $n;
            $text = "Ви обрали $name - $n шт, загальною вартістю $total грн. Підтверджуєте замовлення?";

            $replyMarkup = InlineKeyboardMarkup::make()->addRow(
                InlineKeyboardButton::make('Так', callback_data: 'billing_start'),
                InlineKeyboardButton::make('Повернутися на початок', callback_data: 'cancel'),
            );

            $bot->sendMessage($text, [
                'reply_markup' => $replyMarkup
            ]);
        });

        $this->bot->onCallbackQueryData('billing_start', function (Nutgram $bot) {
            $bot->answerCallbackQuery();
            BillingConversation::begin($bot);
        });
        
        $this->bot->fallback(function (Nutgram $bot) {
            $bot->sendMessage("Sorry, I don't understand. Can you start again with /start command?");
        });
        
        $this->bot->onException(function (Nutgram $bot, \Throwable $exception) {
            print $exception->getMessage();
            error_log($exception);
            $bot->sendMessage('Sorry, something unexpected happened. Can you start again with /start command?');
        });
    }

    private function prepareModels(array $models, DbConnection $db) {
        $initModels = [];
        foreach ($models as $model) {
            $modelObj = new $model($db);
            $initModels[$model] = $modelObj;

            $this->bot->getContainer()->add($model, $modelObj);
        }
        return $initModels;
    }
}
