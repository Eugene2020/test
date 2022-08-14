<?
//Выборка из таблицы уровней программы лояльности

//По условию задачи есть класс для общения с сервером

$userLoyaltyBalance = LoyaltyInfo::loyaltyBalance(); // Получили количество баллов
$userLoyaltyLevelCode = LoyaltyInfo::cardInfo(); // Уровень и номер карты

//Получим уровень программы лояльности пользователя. Раз класс - наследник DataManager, то и getList есть:

$rsLoyaltyLevel = LoyaltyLevels::getList([
    'filter' => [
        'CODE' => $userLoyaltyLevelCode['CODE'], // Например код уровня лежит тут
    ],
    'select' => [
        'CODE', 
        'THRESHOLD', 
        'RECEIVE_PERCENT', 
        'PAYMENT_PERCENT', 
        'SALE_PERCENT', 
        'FREE_DELIVERY_THRESHOLD', 
        'FREE_REJECTION'
    ],
]);

$arLoyaltyLevel = $rsLoyaltyLevel->Fetch(); 

?>