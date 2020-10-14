<?php

namespace App\Models\Traits\Authenticated;

trait LoadAuthenticatedUser
{
    public function loadAuthenticatedUser()
    {
        $this->authenticated_user = ($this->authenticated_user_model)::find($this->authenticated_user_id) ?: null;

        return $this;
    }
}