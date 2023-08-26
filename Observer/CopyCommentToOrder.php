<?php
declare(strict_types=1);

namespace StackExchange\OrderComment\Observer;

use Magento\Framework\DataObject\Copy as ObjectCopyService;
use Magento\Framework\Event\ObserverInterface;

class CopyCommentToOrder implements ObserverInterface
{
    private ObjectCopyService $objectCopyService;

    public function __construct(ObjectCopyService $objectCopyService)
    {
        $this->objectCopyService = $objectCopyService;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        $quote = $observer->getEvent()->getData('quote');

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_quote',
            'to_order',
            $quote,
            $order
        );

        return $this;
    }
}