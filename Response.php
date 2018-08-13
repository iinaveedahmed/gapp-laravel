<?php

namespace Ipaas;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response as LResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */

    /**
     *  Send response with metadata and data
     *
     * @param     $data
     * @param int $status
     *
     * @return Response
     */
    public function sendResponse($data, $status = LResponse::HTTP_OK)
    {
        $response = [
            'meta' => $this->getMeta($status),
            'data' => $data,
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
     *   required={"status", "self"},
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
        $request = request();
        $meta = $this->meta;
        $meta['status'] = $statusCode;
        $meta['self'] = url($request->path());

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
            'code' => $code ?: Str::uuid(),
        ];

        // debug trace if debug is active
        if ($trace) {
            $meta['trace'] = $trace;
        }

        // set meta for response
        $this->setMeta($meta);

        // response data
        $response = [
            'meta' => $this->getMeta($status),
            'messages' => $message,
            'errors' => $errors
        ];

        return response($response, $status, $this->getHeaders());
    }

    /**
     * Send validation/unprocessed entity error
     * @param string $message
     * @param array $errors
     * @return Response
     */
    public function errorValidation($message = 'Unprocessed Entity', $errors = [])
    {
        Log::error($message, $errors);
        return $this->sendError($message, 422, null, $errors);
    }

    /**
     * Send unauthorized error
     * @param string $message
     * @param array $errors
     * @return Response
     */
    public function errorUnauthorized($message = 'Unauthorized', $errors = [])
    {
        Log::error($message, $errors);
        return $this->sendError($message, 401, null, $errors);
    }

    /**
     * Send unauthorized error
     * @param string $message
     * @param array $errors
     * @return Response
     */
    public function errorBadRequest($message = 'Bad Request', $errors = [])
    {
        Log::error($message, $errors);
        return $this->sendError($message, 400, null, $errors);
    }

    /**
     * Send too many request error
     * @param string $message
     * @param array $errors
     * @return Response
     */
    public function errorTooManyRequest($message = 'Too Many Requests', $errors = [])
    {
        Log::error($message, $errors);
        return $this->sendError($message, 429, null, $errors);
    }

    /**
     * Send not found error
     * @param string $message
     * @param array $errors
     * @return Response
     */
    public function errorNotFound($message = 'Not Found', $errors = [])
    {
        Log::error($message, $errors);
        return $this->sendError($message, 400, null, $errors);
    }

    /**
     * Send not implemented error
     * @return Response
     */
    public function errorNotImplemented()
    {
        $message = 'Method not implemented';
        Log::error('Method not implemented');
        return $this->sendError($message, 501);
    }

    /**
     * Send internal server error
     * @param string $message
     * @param array $errors
     * @return Response
     */
    public function errorInternalServer($message = 'Internal Server Error', $errors = [])
    {
        Log::error($message, $errors);
        return $this->sendError($message, 500, null, $errors);
    }
}
