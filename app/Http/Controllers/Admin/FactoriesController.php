<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NftType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Factory as FactoryResource;
use App\Models\Factory;
use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Enum;

class FactoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Factory::query()->with(['launchpads']);
        if (!empty($keyword)) {
            $query->where('version', 'LIKE', "%$keyword%");
        }
        $query->withCount('launchpads');
        $factoriesItems = $query->latest()->paginate($perPage);
        $factories = FactoryResource::collection($factoriesItems);
        return Inertia::render('Admin/Factories/Index', compact('factories'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $foundry = json_decode(File::get(base_path('evm/Foundry.json')));
        return Inertia::render('Admin/Factories/Create', [
            'foundry' => $foundry,
            'config' => collect(config('evm'))->reject(function ($evm) {
                return !is_array($evm) || !isset($evm['chainId']) || !isset($evm['symbol']);
            })->keyBy('chainId'),
            'types' => collect(NftType::cases())->map(function ($type) {
                return [
                    'value' => $type->value,
                    'label' => $type->label(),
                ];
            })->all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'chainId' => ['required', 'integer'],
            'foundry' => ['required', 'string', 'max:44'],
            'contract' => ['required', 'string', 'max:44'],
            'lock' => ['required', 'string', 'max:44'],
            'nft_type' => ['required', new Enum(NftType::class)],
        ]);
        $lockImplementation = json_decode(File::get(base_path('evm/Lock.json')));
        $factoryImplementation = json_decode(File::get(base_path('evm/Factory.json')));
        $curveImplementation = json_decode(File::get(base_path('evm/BondingCurve.json')));
        $factory = new Factory;
        $factory->version = $request->name;
        $factory->chainId = $request->chainId;
        $factory->foundry = $request->foundry;
        $factory->contract = $request->contract;
        $factory->lock = $request->lock;
        $factory->lock_abi = $lockImplementation->abi;
        $factory->factory_abi = $factoryImplementation->abi;
        $factory->abi = $curveImplementation->abi;
        $factory->active = true;
        $factory->nft_type = $request->nft_type ?? NftType::PLANKTON;
        $factory->save();
        return redirect()->route('admin.factories.index')->with('success', 'Factory added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Factory $factory)
    {
        $factory->load(['launchpads']);
        return Inertia::render('Factories/Show', [
            'factory' => new FactoryResource($factory)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Factory $factory)
    {
        $factory->loadCount(['launchpads']);
        return Inertia::render('Admin/Factories/Edit', [
            'factory' => new FactoryResource($factory),
            'types' => collect(NftType::cases())->map(function ($type) {
                return [
                    'value' => $type->value,
                    'label' => $type->label(),
                ];
            })->all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Factory $factory)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'nft_type' => ['nullable', new Enum(NftType::class)],
        ]);
        $factory->version = $request->name;
        $factory->nft_type = $request->nft_type ?? $factory->nft_type ?? NftType::PLANKTON;
        $factory->save();
        return back();
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Factory $factory)
    {
        $factory->active = !$factory->active;
        $factory->save();
        return back()->with('success', $factory->active ? __(' :name Factory Enabled !', ['name' => $factory->name]) : __(' :name  Factory Disabled!', ['name' => $factory->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Factory $factory)
    {
        $factory->delete();
        return back();
    }
}
