<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineSlot;
use App\Models\MachineSlotItem;
use App\Models\MachineUser;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function sellerMachines()
    {
        $machinesIds = MachineUser::where('user_id', auth()->user()->id)->pluck('machine_id');
        $machines = Machine::whereIn('id', $machinesIds)->get();
        return view('seller.machine.listing', compact('machines'));
    }

    public function slots(Request $request, $machineId)
    {
        $slots = MachineSlot::where('machine_id', $machineId)->get();
        return view('seller.machine.slots.listing', compact('slots', 'machineId'));
    }

    public function slotItems(Request $request, $slotId)
    {
        $items = MachineSlotItem::where('machine_slot_id', $slotId)->where('user_id', auth()->user()->id)->get();
        return view('seller.machine.slots.items.listing', compact('items', 'slotId'));
    }

    public function createSlotItem(Request $request, $slotId)
    {
        return view('seller.machine.slots.items.create', compact('slotId'));
    }

    public function addSlotItem(Request $request, $slotId)
    {
        $validated = $request->validate([
            'title' => 'required',
            'price' => 'required|integer',
            'quantity' => 'required|integer',
            'user_id' => 'required|integer',
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'machine_slot_id' => 'required',
        ]);
        if ($request->hasFile('image')) {
            $profileImage = $request->file('image');
            $uploadFolder = 'item_images';
            $filename = $profileImage->getClientOriginalName();
            $profileImage->storeAs($uploadFolder, $filename, 'public');
            $validated['image'] = $uploadFolder . '/' . $filename;
        }

        MachineSlotItem::create($validated);
        return redirect("/seller/machines/slots/item/" . $validated['machine_slot_id'])
            ->withSuccess('Slot item created successfully');
    }

    public function deleteSlotItem(Request $request, $slotId)
    {
        $item = MachineSlotItem::find($slotId);
        $item->delete();
        return redirect()->back()->withSuccess('Slot item deleted successfully');
    }
}
