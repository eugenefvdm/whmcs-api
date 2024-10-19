<?php

namespace Eugenefvdm\Whmcs\Contracts;

interface BillingProvider
{
    public function changePlan(array $data);
}
