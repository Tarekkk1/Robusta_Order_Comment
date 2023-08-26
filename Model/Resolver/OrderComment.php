<?php
declare(strict_types=1);

namespace StackExchange\OrderComment\Model\Resolver;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartRepositoryInterface;

class OrderComment implements ResolverInterface
{
    private $cartRepository;

    private $maskedQuoteIdToQuoteId;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
    ) {
        $this->cartRepository = $cartRepository;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
    }
    
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $maskedCartId = $args['input']['cart_id'];
            $comment = $args['input']['comment'];
    
            $cartId = $this->maskedQuoteIdToQuoteId->execute($maskedCartId);
    
            $quote = $this->cartRepository->get($cartId);
            $quote->setData('comment', $comment);
            $this->cartRepository->save($quote);
    
            return [
                'success' => true,
                'message' => 'Comment added successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Unable to add the comment.'
            ];
        }
    }
    
}