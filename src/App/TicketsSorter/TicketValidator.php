<?php
declare(strict_types=1);


namespace TravelSorter\App\TicketsSorter;


class TicketValidator implements TicketValidatorInterface
{
    /**
     * Check if a ticket has valid information.
     *
     * @param TicketInterface $value
     *
     * @return string|null
     *  Return null is the ticket is valid.
     *  Return a string describing the error if the ticket is invalid.
     */
    public function validate(TicketInterface $value): ?string
    {
        $requiredAttributes = [
            'transport',
            'origin',
            'destiny',
        ];

        foreach ($requiredAttributes as $requiredAttribute) {
            $methodName = 'get' . ucfirst($requiredAttribute);
            $attributeValue = $value->$methodName();
            if (!$attributeValue || !trim($attributeValue)) {
                return sprintf('Missing value for the "%s" attribute.', $requiredAttribute);
            }
        }

        return null;
    }
}