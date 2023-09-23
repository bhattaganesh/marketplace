<?php

namespace Marketplace\Services;

use Marketplace\Models\ItemModel;
use PDO;


class ItemService
{
    private $itemModel;

    public function __construct(PDO $conn)
    {
        $this->itemModel = new ItemModel($conn);
    }

    public function getItemsForSeller($seller)
    {
        return $this->itemModel->getItemsBySeller($seller);
    }

    public function getItemDetails($itemId, $seller)
    {
        return $this->itemModel->getItemById($itemId, $seller);
    }
}
