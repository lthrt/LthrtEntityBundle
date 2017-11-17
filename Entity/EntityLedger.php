<?php

namespace Lthrt\EntityBundle\Entity;

/**
 * EntityLedger.
 *
 * All entities log last change
 *
 * If this interface is implemented, all modificaiton timestamps will be stored
 * The Listener checks for the presence of this trait
 */
interface EntityLedger
{
}
