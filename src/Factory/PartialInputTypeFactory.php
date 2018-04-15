<?php

declare(strict_types=1);

namespace GraphQL\Doctrine\Factory;

use GraphQL\Doctrine\Utils;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

/**
 * A factory to create an InputObjectType from a Doctrine entity but with
 * all fields as optional and without default values.
 */
class PartialInputTypeFactory extends AbstractTypeFactory
{
    /**
     * Create an InputObjectType from a Doctrine entity,
     * but will all fields as optional and without default values.
     *
     * @param string $className class name of Doctrine entity
     *
     * @return InputObjectType
     */
    public function create(string $className): Type
    {
        $type = clone $this->types->getInput($className);
        $fieldsGetter = $type->config['fields'];

        $optionalFieldsGetter = function () use ($fieldsGetter): array {
            $optionalFields = [];
            foreach ($fieldsGetter() as $field) {
                if ($field['type'] instanceof NonNull) {
                    $field['type'] = $field['type']->getWrappedType();
                }

//                $field['defaultValue'] = null;
                unset($field['defaultValue']);

                $optionalFields[] = $field;
            }

            return $optionalFields;
        };
        $type->config['fields'] = $optionalFieldsGetter;
        $type->name = Utils::getPartialInputTypeName($className);

        return $type;
    }
}
