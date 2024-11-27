<?php

namespace Tqdev\PhpCrudApi\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tqdev\PhpCrudApi\Middleware\Router\Router;
use Tqdev\PhpCrudApi\Record\ErrorCode;
use Tqdev\PhpCrudApi\Record\RecordService;
use Tqdev\PhpCrudApi\RequestUtils;

class RecordController
{
    private $service;
    private $responder;

    public function __construct(Router $router, Responder $responder, RecordService $service)
    {
        $router->register('GET', '/records/*', array($this, '_list'));
        $router->register('POST', '/records/*', array($this, 'create'));
        $router->register('GET', '/records/*/*', array($this, 'read'));
        $router->register('PUT', '/records/*/*', array($this, 'update'));
        $router->register('DELETE', '/records/*/*', array($this, 'delete'));
        $router->register('PATCH', '/records/*/*', array($this, 'increment'));
        $this->service = $service;
        $this->responder = $responder;
    }

    public function _list(ServerRequestInterface $request): ResponseInterface
    {
        $table = RequestUtils::getPathSegment($request, 2);
        $params = RequestUtils::getParams($request);
        if (!$this->service->hasTable($table)) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $table);
        }
        return $this->responder->success($this->service->_list($table, $params));
    }

    public function read(ServerRequestInterface $request): ResponseInterface
    {
        $table = RequestUtils::getPathSegment($request, 2);
        if (!$this->service->hasTable($table)) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $table);
        }
        if (!$this->service->hasPrimaryKey($table)) {
            return $this->responder->error(ErrorCode::PRIMARY_KEY_NOT_FOUND, $table);
        }
        $id = RequestUtils::getPathSegment($request, 3);
        $params = RequestUtils::getParams($request);
        if (strpos($id, ',') !== false) {
            $ids = explode(',', $id);
            $argumentLists = array();
            for ($i = 0; $i < count($ids); $i++) {
                $argumentLists[] = array($table, $ids[$i], $params);
            }
            return $this->responder->multi($this->multiCall([$this->service, 'read'], $argumentLists));
        } else {
            $response = $this->service->read($table, $id, $params);
            if ($response === null) {
                return $this->responder->error(ErrorCode::RECORD_NOT_FOUND, $id);
            }
            return $this->responder->success($response);
        }
    }

    private function multiCall(callable $method, array $argumentLists): array
    {
        $result = array();
        $success = true;
        $this->service->beginTransaction();
        foreach ($argumentLists as $arguments) {
            try {
                $result[] = call_user_func_array($method, $arguments);
            } catch (\Throwable $e) {
                $success = false;
                $result[] = $e;
            }
        }
        if ($success) {
            $this->service->commitTransaction();
        } else {
            $this->service->rollBackTransaction();
        }
        return $result;
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $table = RequestUtils::getPathSegment($request, 2);
        if (!$this->service->hasTable($table)) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $table);
        }
        if (!$this->service->hasPrimaryKey($table)) {
            return $this->responder->error(ErrorCode::PRIMARY_KEY_NOT_FOUND, $table);
        }
        if ($this->service->getType($table) != 'table') {
            return $this->responder->error(ErrorCode::OPERATION_NOT_SUPPORTED, 'create');
        }
        $record = $request->getParsedBody();
        if ($record === null) {
            return $this->responder->error(ErrorCode::HTTP_MESSAGE_NOT_READABLE, '');
        }
        $params = RequestUtils::getParams($request);
        if (is_array($record)) {
            $argumentLists = array();
            foreach ($record as $r) {
                $argumentLists[] = array($table, $r, $params);
            }
            return $this->responder->multi($this->multiCall([$this->service, 'create'], $argumentLists));
        } else {
            return $this->responder->success($this->service->create($table, $record, $params));
        }
    }

    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $table = RequestUtils::getPathSegment($request, 2);
        if (!$this->service->hasTable($table)) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $table);
        }
        if (!$this->service->hasPrimaryKey($table)) {
            return $this->responder->error(ErrorCode::PRIMARY_KEY_NOT_FOUND, $table);
        }
        if ($this->service->getType($table) != 'table') {
            return $this->responder->error(ErrorCode::OPERATION_NOT_SUPPORTED, 'update');
        }
        $id = RequestUtils::getPathSegment($request, 3);
        $params = RequestUtils::getParams($request);
        $record = $request->getParsedBody();
        if ($record === null) {
            return $this->responder->error(ErrorCode::HTTP_MESSAGE_NOT_READABLE, '');
        }
        $ids = explode(',', $id);
        if (is_array($record)) {
            if (count($ids) != count($record)) {
                return $this->responder->error(ErrorCode::ARGUMENT_COUNT_MISMATCH, $id);
            }
            $argumentLists = array();
            for ($i = 0; $i < count($ids); $i++) {
                $argumentLists[] = array($table, $ids[$i], $record[$i], $params);
            }
            return $this->responder->multi($this->multiCall([$this->service, 'update'], $argumentLists));
        } else {
            if (count($ids) != 1) {
                return $this->responder->error(ErrorCode::ARGUMENT_COUNT_MISMATCH, $id);
            }
            return $this->responder->success($this->service->update($table, $id, $record, $params));
        }
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $table = RequestUtils::getPathSegment($request, 2);
        if (!$this->service->hasTable($table)) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $table);
        }
        if (!$this->service->hasPrimaryKey($table)) {
            return $this->responder->error(ErrorCode::PRIMARY_KEY_NOT_FOUND, $table);
        }
        if ($this->service->getType($table) != 'table') {
            return $this->responder->error(ErrorCode::OPERATION_NOT_SUPPORTED, 'delete');
        }
        $id = RequestUtils::getPathSegment($request, 3);
        $params = RequestUtils::getParams($request);
        $ids = explode(',', $id);
        if (count($ids) > 1) {
            $argumentLists = array();
            for ($i = 0; $i < count($ids); $i++) {
                $argumentLists[] = array($table, $ids[$i], $params);
            }
            return $this->responder->multi($this->multiCall([$this->service, 'delete'], $argumentLists));
        } else {
            return $this->responder->success($this->service->delete($table, $id, $params));
        }
    }

    public function increment(ServerRequestInterface $request): ResponseInterface
    {
        $table = RequestUtils::getPathSegment($request, 2);
        if (!$this->service->hasTable($table)) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $table);
        }
        if (!$this->service->hasPrimaryKey($table)) {
            return $this->responder->error(ErrorCode::PRIMARY_KEY_NOT_FOUND, $table);
        }
        if ($this->service->getType($table) != 'table') {
            return $this->responder->error(ErrorCode::OPERATION_NOT_SUPPORTED, 'increment');
        }
        $id = RequestUtils::getPathSegment($request, 3);
        $record = $request->getParsedBody();
        if ($record === null) {
            return $this->responder->error(ErrorCode::HTTP_MESSAGE_NOT_READABLE, '');
        }
        $params = RequestUtils::getParams($request);
        $ids = explode(',', $id);
        if (is_array($record)) {
            if (count($ids) != count($record)) {
                return $this->responder->error(ErrorCode::ARGUMENT_COUNT_MISMATCH, $id);
            }
            $argumentLists = array();
            for ($i = 0; $i < count($ids); $i++) {
                $argumentLists[] = array($table, $ids[$i], $record[$i], $params);
            }
            return $this->responder->multi($this->multiCall([$this->service, 'increment'], $argumentLists));
        } else {
            if (count($ids) != 1) {
                return $this->responder->error(ErrorCode::ARGUMENT_COUNT_MISMATCH, $id);
            }
            return $this->responder->success($this->service->increment($table, $id, $record, $params));
        }
    }
}
