<?php

namespace Zencoder\Services\Zencoder;

class Accounts extends Base
{
    /**
     * Create a Zencoder account.
     *
     * @param array $account Array of attributes to use when creating the account
     * @param array $params  Optional overrides
     *
     * @return Account The object representation of the resource
     */
    public function create($account = null, $params = [])
    {
        if (is_string($account)) {
            $json = trim($account);
        } elseif (is_array($account)) {
            $json = json_encode($account);
        } else {
            throw new \Exception('Account parameters required to create account.');
        }

        return new Account($this->proxy->createData('account', $json, $params));
    }

    /**
     * Return details of your Zencoder account.
     *
     * @param array $params Optional overrides
     *
     * @return Account The object representation of the resource
     */
    public function details($params = [])
    {
        return new Account($this->proxy->retrieveData('account.json', [], $params));
    }

    /**
     * Put your account into integration mode.
     *
     * @param array $params Optional overrides
     *
     * @return bool If the operation was successful
     */
    public function integration($params = [])
    {
        return $this->proxy->updateData('account/integration', '', $params);
    }

    /**
     * Put your account into live mode.
     *
     * @param array $params Optional overrides
     *
     * @return bool If the operation was successful
     */
    public function live($params = [])
    {
        return $this->proxy->updateData('account/live', '', $params);
    }
}
