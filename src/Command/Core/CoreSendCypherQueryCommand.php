<?php

/**
 * This file is part of the "-[:NEOXYGEN]->" NeoClient package
 *
 * (c) Neoxygen.io <http://neoxygen.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Neoxygen\NeoClient\Command\Core;

use Neoxygen\NeoClient\Command\AbstractCommand;

class CoreSendCypherQueryCommand extends AbstractCommand
{
    const METHOD = 'POST';

    const PATH = '/db/data/transaction/commit';

    public $query;

    public $parameters;

    public $resultDataContents;

    public function setArguments($query, array $parameters = array(), array $resultDataContents = array())
    {
        $this->query = $query;
        $this->parameters = $parameters;
        $this->resultDataContents = $resultDataContents;

        return $this;
    }

    public function execute()
    {
        return $this->httpClient->send(self::METHOD, self::PATH, $this->prepareBody(), $this->connection);
    }

    public function prepareBody()
    {
        $statement = array();
        $statement['statement'] = $this->query;
        if (!empty($this->parameters)) {
            $statement['parameters'] = $this->parameters;
        }
        if (!empty($this->resultDataContents)) {
            $statement['resultDataContents'] = $this->resultDataContents;
        }
        $body = array(
            'statements' => array(
                $statement
            )
        );

        return json_encode($body);
    }

    public function getPath()
    {
        return $this->getBaseUrl() . '/db/data/transaction/commit';
    }
}
