<?php

namespace App\Bot\Commands;

use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'A lovely start command';

    private $attachedHandlers = false;

    public function handle(Nutgram $bot): void
    {
        $this->attachHandlersOnReply($bot);

        $replyMarkup = InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make('Look at products', callback_data: 'products'),
            InlineKeyboardButton::make('Check my orders', callback_data: 'orders'),
        );

        $bot->sendMessage(
            'Choose available action:',
            [
                'reply_markup' => $replyMarkup
            ]
        );
    }

    private function attachHandlersOnReply($bot) {
        if ($this->attachedHandlers) {
            return;
        }

        $this->attachedHandlers = true;
        
        // Get the Message object
        // $bot->message();
        // Access the Chat object
        // $bot->chat();

        $bot->onCallbackQueryData('products', function (Nutgram $bot) {
            $categoryModel = $bot->getContainer()->get(CategoryModel::class);
            $categories = $categoryModel->getAll();

            $buttons = [];

            foreach ($categories as $category) {
                $buttons[] = InlineKeyboardButton::make($category['name'], callback_data: 'category_' . $category['id']);
            }

            $replyMarkup = InlineKeyboardMarkup::make()->addRow(...$buttons);

            $bot->answerCallbackQuery();

            $bot->sendMessage(
                'Choose category:',
                [
                    'reply_markup' => $replyMarkup
                ]
            );
        });

        $bot->onCallbackQueryData('products', function (Nutgram $bot) {
            $categoryModel = $bot->getContainer()->get(CategoryModel::class);
            $categories = $categoryModel->getAll();

            $buttons = [];

            foreach ($categories as $category) {
                $buttons[] = InlineKeyboardButton::make($category['name'], callback_data: 'category ' . $category['id']);
            }

            $replyMarkup = InlineKeyboardMarkup::make()->addRow(...$buttons);

            $bot->answerCallbackQuery();

            $bot->sendMessage(
                '*Choose* category:',
                [
                    'reply_markup' => $replyMarkup,
                    'parse_mode' => ParseMode::MARKDOWN,
                ]
            );
        });

        $bot->onCallbackQueryData('category {param}', function (Nutgram $bot, $categoryId) {
            $productModel = $bot->getContainer()->get(ProductModel::class);
            $products = $productModel->getActiveForCategory($categoryId);

            $replyMarkup = InlineKeyboardMarkup::make();

            foreach ($products as $product) {
                $button = InlineKeyboardButton::make(
                    "'" . $product['name'] . "' за " . $product["price"] . "грн", 
                    callback_data: 'product ' . $product['id']
                );
                $replyMarkup->addRow($button);
            }


            $bot->sendMessage(
                'Choose product:',
                [
                    'reply_markup' => $replyMarkup
                ]
            );
            $bot->answerCallbackQuery();
        });

        $bot->onCallbackQueryData('product {param}', function (Nutgram $bot, $productId) {
            $productModel = $bot->getContainer()->get(ProductModel::class);
            $productInfo = $productModel->getFullInfoById($productId);

            $text = $productInfo['name'] . ". " . $productInfo['desc']. '. За ' . $productInfo['price'] . 'грн. Залишилося на складі: '. 
                $productInfo['count_available'] . '. Бажаєте купити?';

            $replyMarkup = InlineKeyboardMarkup::make()->addRow(
                InlineKeyboardButton::make('Так', callback_data: 'order_product ' . $productId),
                InlineKeyboardButton::make('Повернутися на початок', callback_data: 'cancel'),
            );


            $bot->sendMessage(
                $text,
                [
                    'reply_markup' => $replyMarkup
                ]
            );
            $bot->answerCallbackQuery();
        });

        $bot->onCallbackQueryData('order_product {param}', function (Nutgram $bot, $productId) {
            $bot->setUserData('order_product_id', $productId);
            $bot->sendMessage("Скільки хочете взяти? Введіть число більше 0");
            $bot->answerCallbackQuery();
        });

        $bot->onCallbackQueryData('cancel', function (Nutgram $bot) {
            $bot->deleteUserData('order_product_id');
            $bot->deleteUserData('order_product_num');
            
            $this->handle($bot);
        });

        $bot->onCallbackQueryData('orders', function (Nutgram $bot) {
            $orderModel = $bot->getContainer()->get(OrderModel::class);
            $orders = $orderModel->getAllByUserId($bot->chat()->id);

            $replyMarkup = InlineKeyboardMarkup::make();

            foreach ($orders as $order) {
                $text = sprintf("%s: (%s, %s, %s, %s)", 
                    $order['id'], $order['user_email'], $order['user_name'], $order['user_address'], $order['user_phone']
                );
                
                $button = InlineKeyboardButton::make(
                    $text,
                    callback_data: 'order ' . $order['id']
                );
                $replyMarkup->addRow($button);
            }


            $bot->sendMessage(
                'Choose order:',
                [
                    'reply_markup' => $replyMarkup
                ]
            );
            $bot->answerCallbackQuery();
        });

        $bot->onCallbackQueryData('order {id}', function (Nutgram $bot, $orderId) {
            $orderModel = $bot->getContainer()->get(OrderModel::class);
            $orderItems = $orderModel->getById($orderId);

            if (empty($orderItems)) {
                $bot->sendMessage('У вас немає такого замовлення');
                $bot->answerCallbackQuery();
                return;
            }

            $generalOrderInfo = [
                'id' => $orderItems[0]['id'],
                'address' => $orderItems[0]['address'],
                'name' => $orderItems[0]['name'],
                'phone' => $orderItems[0]['phone'],
            ];



            $total = 0;
            $tableData = [];
            $tableData[] = ['Назва', 'Кількість', 'Ціна за 1', 'Ціна загалом'];
            foreach ($orderItems as $orderItem) {
                $tableData[] = [$orderItem['product_name'], $orderItem['count'], $orderItem['price'], $orderItem['count'] * $orderItem['price']];
                $total += $orderItem['count'] * $orderItem['price'];
            }

            $template = <<<TEXT
            Замовлення №%s
            Доставка: %s, %s, %s
            Загалом: %s
            TEXT;

            $text = sprintf($template, 
                $generalOrderInfo['id'], $generalOrderInfo['address'], $generalOrderInfo['name'], $generalOrderInfo['phone'], $total
            );


            $bot->sendMessage($text);
            $bot->sendMessage($this->generateMarkdownTable($tableData), ['parse_mode' => 'MarkdownV2']);
            $bot->answerCallbackQuery();
        });
    }

    private function generateMarkdownTable($data) {    
        $maxLengths = [];
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                if (!isset($maxLengths[$key]) || mb_strlen($value) > $maxLengths[$key]) {
                    $maxLengths[$key] = mb_strlen($value);
                }
            }
        }
        
        $formattedRows = [];
        foreach ($data as $row) {
            $formattedRow = [];
            foreach ($row as $key => $value) {
                $formattedRow[] = $this->myStrPad($value, $maxLengths[$key]);
            }
            $formattedRows[] = implode(" | ", $formattedRow);
        }
        
        return "```\n" . implode("\n", $formattedRows) . "\n```";  
    }

    private function myStrPad($string, $len) {
        if (mb_strlen($string) >= $len) {
            return $string;
        }

        $repeat = str_repeat(" ", $len - mb_strlen($string));

        return $string . $repeat;
    }
}
