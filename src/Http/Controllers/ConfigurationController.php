<?php

namespace Credpal\Configurations\Http\Controllers;

use Credpal\Configurations\Http\Requests\ConfigurationRequest;
use Credpal\Configurations\Services\ConfigurationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController
{
    public function __construct(protected ConfigurationService $configurationService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return new JsonResponse([
                'configurations' => $this->configurationService->getAll(
                    explode(',', $request->query('key', ''))
                )
            ]);
        }

        return view('');
    }

    /**
     * Display the specified resource.
     *
     * @param  string $key
     * @return JsonResponse
     */
    public function show(string $key): JsonResponse
    {
        return new JsonResponse([
            'configurations' => $this->configurationService->get($key)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ConfigurationRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(ConfigurationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $configurations = $this->configurationService->updateOrCreate($request->validated()['configurations']);
            DB::commit();
            return new JsonResponse(compact('configurations'), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Reset all configurations or the ones whose key is specified.
     *
     * @param ?string $keys
     * @return JsonResponse
     * @throws Exception
     */
    public function reset(?string $keys): JsonResponse
    {
        try {
            DB::beginTransaction();
            if (!$keys) {
                $this->configurationService->resetAll();
            } else {
                $this->configurationService->reset(explode(',', $keys));
            }
            DB::commit();
            return new JsonResponse();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Delete all configurations or the ones whose key is specified
     *
     * @param ?string $keys
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(?string $keys): JsonResponse
    {
        try {
            DB::beginTransaction();
            if (!$keys) {
                $this->configurationService->deleteAll();
            } else {
                $this->configurationService->delete(explode(',', $keys));
            }
            DB::commit();
            return new JsonResponse(status: Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
