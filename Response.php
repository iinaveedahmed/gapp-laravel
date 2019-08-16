<?php

namespace Ipaas\Gapp;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Response as StatusCode;

class Response extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Metadata
     * @var array
     */
    protected $meta;

    /**
     * Header
     * @var array
     */
    protected $headers;

    /**
     * Swg. Response
     * @SWG\Definition(
     *   definition="ResponseModel",
     *   type="object",
     *   required={"status", "self"},
     *   @SWG\Property(
     *        property="meta",
     *        type="object",
     *        required={"status", "self"},
     *        @SWG\Property(
     *             property="status",
     *             type="integer"
     *        ),
     *        @SWG\Property(
     *             property="self",
     *             type="string"
     *        )
     *   ),
     *   @SWG\Property(
     *        property="data",
     *        type="object"
     *   ),
     * )
     * @param $data
     * @param $status
     * @return \Illuminate\Contracts\Routing\ResponseFactory|StatusCode
     */

    /**
     *  Send response with metadata and data
     *
     * @param     $data
     * @param int $status
     *
     * @return Response
     */
    public function sendResponse($data, $status = StatusCode::HTTP_OK)
    {
        $response = [
            'data' => $data,
            'meta' => $this->getMeta($status),
        ];

        return response($response, $status, $this->getHeaders());
    }

    /**
     * Set header data
     *
     * @param array $headers
     * @return Response
     */
    public function setHeaders($headers = [])
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Return header data
     * @return array
     */
    private function getHeaders()
    {
        return $this->headers ?? [];
    }

    /**
     * Swg. Meta Data
     * @SWG\Definition(
     *   definition="MetaModel",
     *   type="object",
     *   required={"request_id", "status", "self"},
     *   @SWG\Property(
     *       property="request_id",
     *       type="string"
     *   ),
     *   @SWG\Property(
     *        property="status",
     *        type="integer"
     *   ),
     *   @SWG\Property(
     *        property="self",
     *        type="string"
     *   ),
     *   @SWG\Property(
     *       property="code",
     *       type="string"
     *   ),
     *   @SWG\Property(
     *       property="message",
     *       type="string"
     *   ),
     *   @SWG\Property(
     *       property="trace",
     *       type="array",
     *       @SWg\Items()
     *   )
     * )
     */

    /**
     * Set metadata
     *
     * @param array $meta
     * @return Response
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * Return metadata with status
     * @param integer $statusCode
     *
     * @return array headers
     */
    private function getMeta($statusCode)
    {
        $meta = $this->meta;
        $meta['status'] = $statusCode;
        $meta['self'] = url(request()->path());
        $meta['request_id'] = ilog()->getRequestId();

        return $meta;
    }

    /**
     * Swg. error response
     * @SWG\Definition(
     *         definition="ErrorModel",
     *         type="object",
     *         required={"status", "message", "code"},
     *         @SWG\Property(
     *             property="meta",
     *             required={"status", "self", "message", "code"},
     *             @SWG\Schema(ref="#/definitions/MetaModel")
     *         )
     *     )
     */

    /**
     * Send error response
     *
     * @param $message
     * @param $status
     * @param $code
     * @param $errors
     * @param $trace
     *
     * @return Response
     */
    public function sendError($message, $status, $code = null, $errors = null, $trace = null)
    {
        // set meta data for error
        $meta = [
            'message' => $message,
            'code' => $code ?? Str::uuid(),
        ];

        // debug trace if debug is active
        if ($trace) {
            $meta['trace'] = $trace;
        }

        // set meta for response
        $this->setMeta($meta);

        // response data
        $response = [
            'messages' => $message,
            'errors' => $errors,
            'meta' => $this->getMeta($status),
        ];

        return response($response, $status, $this->getHeaders());
    }

    /**
     * Send validation/unprocessed entity error
     * @return Response
     */
    public function sendErrorUnprocessable($message = 'Unprocessed Entity', $errors = [])
    {
        return $this->sendError($message, StatusCode::HTTP_UNPROCESSABLE_ENTITY, null, $errors);
    }

    /**
     * Send unauthorized error
     * @return Response
     */
    public function sendErrorUnauthorized($message = 'Unauthorized', $errors = [])
    {
        return $this->sendError($message, StatusCode::HTTP_UNAUTHORIZED, null, $errors);
    }

    /**
     * Send bad request error
     * @return Response
     */
    public function sendErrorBadRequest($message = 'Bad Request', $errors = [])
    {
        return $this->sendError($message, StatusCode::HTTP_BAD_REQUEST, null, $errors);
    }

    /**
     * Send too many request error
     * @return Response
     */
    public function sendErrorTooManyRequest($message = 'Too Many Requests', $errors = [])
    {
        return $this->sendError($message, StatusCode::HTTP_TOO_MANY_REQUESTS, null, $errors);
    }

    /**
     * Send not found error
     * @return Response
     */
    public function sendErrorNotFound($message = 'Not Found', $errors = [])
    {
        return $this->sendError($message, StatusCode::HTTP_NOT_FOUND, null, $errors);
    }

    /**
     * Send not implemented error
     * @return Response
     */
    public function sendErrorNotImplemented($message = 'Method not implemented')
    {
        return $this->sendError($message, StatusCode::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * Send internal server error
     * @return Response
     */
    public function sendErrorInternalServer($message = 'Internal Server Error', $errors = [])
    {
        return $this->sendError($message, StatusCode::HTTP_INTERNAL_SERVER_ERROR, null, $errors);
    }
}
