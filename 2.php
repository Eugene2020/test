<?

// перерасчет корзины

use Bitrix\Sale;

class OrderLoyalty {

    public static function applyBonusesToBasket($usedBonuses) {

        $userLoyaltyBalance = LoyaltyInfo::loyaltyBalance();

        if ($usedBonuses > $userLoyaltyBalance) {
            echo 'Недостаточно баллов';
            return;
        };

        $fUserId = Sale\Fuser::getId();
        $context = Bitrix\Main\Context::getCurrent()->getSite();

        $basket = Sale\Basket::loadItemsForFUser($fUserId, $context);
        $quantity = $basket->getField('QUANTITY');
        $totalPrice = $basket->getPrice(); 
    
        // получаем максимально возможное количество баллов для списания
        $maximumBonuses = 0;
        foreach ($basket as $basketItem) {
            $currentPrice = $basketItem->getPrice();
            $currentDiscount = $basketItem->getField('DISCOUNT_PRICE');
            if ($currentDiscount && $currentDiscount > 0) { // если у товара в корзине уже есть скидка
                $maximumBonuses += ($currentPrice / 100) * 10;
            } else { // если у товара в корзине нет скидки
                $maximumBonuses += ($currentPrice / 100) * 15;
            };
        }
    
        // Применяем или не применяем скидки от баллов
        if ($maximumBonuses > $usedBonuses) {
            $bonusesForApplyToItem = $usedBonuses / $quantity;
            foreach ($basket as $basketItem) {
                $currentPrice = $basketItem->getPrice();
                $item->setFields(array(
                    'CUSTOM_PRICE' => $currentPrice - $bonusesForApplyToItem,
                ));    
            }
        } else {
            echo 'Вы можете использовать не более '.$maximumBonuses.' баллов';
        }
    
        $basket->save();
        return $basket;
    }
}

?>