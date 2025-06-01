<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Uploads;
use App\Enums\NftType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Nft as NftResource;
use App\Models\Nft;
use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Enum;

class NftsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Nft::query();
        if (!empty($keyword)) {
            $query->where('chainId', 'LIKE', "%$keyword%")
                ->orWhere('contract', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('symbol', 'LIKE', "%$keyword%")
                ->orWhere('abi', 'LIKE', "%$keyword%")
                ->orWhere('metadata', 'LIKE', "%$keyword%")
                ->orWhere('active', 'LIKE', "%$keyword%");
        }
        $nftsItems = $query->latest()->paginate($perPage);
        $nfts = NftResource::collection($nftsItems);
        return Inertia::render('Admin/Nfts/Index', compact('nfts'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $factory = json_decode(File::get(base_path('evm/NftFactory.json')));
        return Inertia::render('Admin/Nfts/Create', [
            'factory' => $factory,
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
            'type' => ['required', 'string', new Enum(NftType::class)],
            'chainId' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'symbol' => ['required', 'string'],
            'contract' => ['required', 'string'],
            'logo_uri' => ['nullable', 'required_if:logo_upload,false', 'string'],
            'logo_upload' => ['required', 'boolean'],
            'logo_path' => ['nullable', 'required_if:logo_upload,true'],
        ]);
        $factory = json_decode(File::get(base_path('evm/NFTImplementation.json')));
        $nft = new Nft;
        $nft->chainId = $request->chainId;
        $nft->name = $request->name;
        $nft->symbol = $request->symbol;
        $nft->contract = $request->contract;
        $nft->abi = $factory->abi;
        $nft->type = $request->type;
        $nft->metadata = [];
        $nft->active = true;
        $nft->save();
        if ($request->logo_upload) {
            $upload = app(Uploads::class)->upload($request, $nft, 'logo');
            $nft->image = $upload->url;
            $nft->save();
        }
        return redirect()->route('admin.nfts.index')->with('success', 'Nft added!');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateMetadata(Request $request, Nft $nft)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|url',
            'external_url' => 'nullable|url',
            'animation_url' => 'nullable|url',
            'background_color' => 'nullable|string|size:6',
            'attributes' => 'required|array',
            'attributes.*.trait_type' => 'required|string',
            'attributes.*.value' => 'required|string',
        ]);
        $metadata = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $request->input('image'),
            'external_url' => $request->input('external_url'),
            'animation_url' => $request->input('animation_url'),
            'background_color' => $request->input('background_color'),
            'attributes' => array_filter($request->input('attributes', []), function ($attr) {
                return !empty(trim($attr['trait_type'])) && !empty(trim($attr['value']));
            })
        ];
        $metadata = array_filter($metadata, function ($value) {
            return !is_null($value) && (!is_string($value) || strlen($value) > 0);
        });
        // Update the NFT metadata
        $nft->update([
            'metadata' => $metadata
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Nft $nft)
    {

        return Inertia::render('Admin/Nfts/Show', [
            'nft' => new NftResource($nft)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Nft $nft)
    {

        return Inertia::render('Admin/Nfts/Edit', [
            'nft' => new NftResource($nft),
            'types' => collect(NftType::cases())->map(function ($type) {
                return [
                    'value' => $type->value,
                    'label' => $type->label(),
                ];
            })->all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function metadata(Request $request, Nft $nft)
    {

        return Inertia::render('Admin/Nfts/Metadata', [
            'nft' => new NftResource($nft)
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
    public function update(Request $request, Nft $nft)
    {
        $request->validate([
            'logo_uri' => ['nullable', 'required_if:logo_upload,false', 'string'],
            'logo_upload' => ['required', 'boolean'],
            'logo_path' => ['nullable', 'required_if:logo_upload,true'],
        ]);
        if ($request->logo_upload) {
            $upload = app(Uploads::class)->upload($request, $nft, 'logo');
            $nft->image = $upload->url;
            $nft->save();
        }
        return back()->with('success', 'Nft updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Nft $nft)
    {
        $nft->active = !$nft->active;
        $nft->save();
        return back()->with('success', $nft->active ? __(' :name Nft Enabled !', ['name' => $nft->name]) : __(' :name  Nft Disabled!', ['name' => $nft->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Nft $nft)
    {
        $nft->delete();
        return redirect()->route('admin.nfts.index')->with('success', 'Nft deleted!');
    }
}
