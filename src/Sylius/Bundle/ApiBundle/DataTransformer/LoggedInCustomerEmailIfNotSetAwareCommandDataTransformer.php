<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\LoggedInCustomerEmailIfNotSetAwareInterface;
use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class LoggedInCustomerEmailIfNotSetAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->getCustomer();

        /** @var CustomerEmailAwareInterface|mixed $object */
        Assert::isInstanceOf($object, LoggedInCustomerEmailIfNotSetAwareInterface::class);

        if ($customer === null) {
            return $object;
        }

        if ($object->getEmail() === null) {
            $object->setEmail($customer->getEmail());
        }

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof LoggedInCustomerEmailIfNotSetAwareInterface;
    }

    private function getCustomer(): ?CustomerInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->userContext->getUser();
        if ($user instanceof ShopUserInterface) {
            return $user->getCustomer();
        }

        return null;
    }
}
