<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Tests\Validator;

use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Fresh\Bundle\DoctrineEnumBundle\Validator\Constraints\EnumValidator;
use Fresh\Bundle\DoctrineEnumBundle\Validator\Constraints\Enum;
use Fresh\Bundle\DoctrineEnumBundle\Tests\BasketballPositionType;

/**
 * EnumValidatorTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnumValidator
     */
    private $enumValidator;

    /**
     * @var \Symfony\Component\Validator\ExecutionContext
     */
    private $context;

    /**
     * Set up EnumValidator
     */
    public function setUp()
    {
        $this->enumValidator = new EnumValidator();

        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test that creation of Enum Constraint without type class should throw ConstraintDefinitionException
     *
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function testExceptionEntityNotSpecified()
    {
        $constraint = new Enum();
        $this->enumValidator->validate(BasketballPositionType::POINT_GUARD, $constraint);
    }

    /**
     * Test valid basketball position
     */
    public function testValidBasketballPositionType()
    {
        $constraint = new Enum(array('entity' => 'Fresh\Bundle\DoctrineEnumBundle\Tests\BasketballPositionType'));

        $this->context
            ->expects($this->never())
            ->method('addViolation');

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate(BasketballPositionType::SMALL_FORWARD, $constraint);
    }

    /**
     * Test invalid basketball position
     */
    public function testInvalidBasketballPositionType()
    {
        $constraint = new Enum(array('entity' => '\Fresh\Bundle\DoctrineEnumBundle\Tests\BasketballPositionType'));

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with(
                $this->equalTo('The value you selected is not a valid choice.'),
                $this->equalTo(array('{{ value }}' => 'Pitcher'))
            );

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate('Pitcher', $constraint); // It is not baseball =)
    }
}
