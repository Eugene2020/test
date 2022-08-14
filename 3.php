<?

// класс с обработчиками событий

use Bitrix\Main;

Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderBeforeSaved',
    ['EventsClass', 'applyLoyalty']
);

class EventsClass {

    public static function applyLoyalty(Main\Event $event) {

        $order = $event->getParameter('ENTITY');

        $isBonusesUsed = $order->getField('isBonusesUsed');
        $bonusesUsed = $order->getField('spentBonusesCount');
        $userCard = LoyaltyInfo::cardInfo();

        if ($isBonusesUsed == 'Y') {

            $basket = OrderLoyalty::applyBonueseToBasket();

            LoyaltyInfo::adjustRewardPoints($order->getField('spentBonusesCount'), 'decrease'); // допустим так списываются бонусы в классе к серверу программы лояльности

            if ($bonusesUsed > $arLoyaltyLevel['FREE_DELIVERY_THRESHOLD'] && $userCard["LEVEL"] == "Gold") { // бесплатная доставка, если проходит по условиям

                $deliveryId = $order->getDeliveryPrice();
                $service = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);

                $deliveryData = [
                    'CUSTOM_PRICE_DELIVERY' => 'Y',
                    'PRICE_DELIVERY' => '0',
                 ];

                $shipment->setFields($deliveryData);

            }

            $order->doFinalAction(true);
            $result = $order->save();
            $orderId = $order->getId();

            return $orderId;

        } 

        if ($isBonusesUsed == 'N') {

            $orderPrice = $order->getPrice();

            $receiveBonusesPercent = $arLoyaltyLevel['RECEIVE_PERCENT'];
            $bonusesForUser = ($orderPrice / 100) * $receiveBonusesPercent;

            LoyaltyInfo::adjustRewardPoints($bonusesForUser, 'increase'); // а так начисляются

            if ($bonusesUsed > $arLoyaltyLevel['FREE_DELIVERY_THRESHOLD'] && $userCard["LEVEL"] ==  "Gold") { // бесплатная доставка, если проходит по условиям

                $deliveryId = $order->getDeliveryPrice();
                $service = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);

                $deliveryData = [
                    'CUSTOM_PRICE_DELIVERY' => 'Y',
                    'PRICE_DELIVERY' => '0',
                 ];

                $shipment->setFields($deliveryData);

            }

            $order->doFinalAction(true);
            $result = $order->save();
            $orderId = $order->getId();

            return $orderId;

        }
        
    }
}

?>