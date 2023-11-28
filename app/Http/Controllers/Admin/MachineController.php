<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineSlot;
use App\Models\MachineSlotItem;
use App\Models\MachineUser;
use App\Models\User;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function listing(Request $request)
    {
        $machines = Machine::all();
        return view('admin.machines.listing', compact('machines'));
    }

    public function create(Request $request)
    {
        return view('admin.machines.create');
    }

    public function addMachine(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
        ]);
        Machine::create($validated);
        return redirect('/admin/machines')
            ->withSuccess('Machine Created Successfully');
    }

    public function delete(Request $request, $id)
    {
        $machine = Machine::find($id);
        $machine->delete();
        return redirect('/admin/machines')
            ->withSuccess('Machine deleted Successfully');
    }

    public function slots(Request $request, $machineId)
    {
        $slots = MachineSlot::where('machine_id', $machineId)->get();
        return view('admin.machines.slots.listing', compact('slots', 'machineId'));
    }

    public function createSlot(Request $request, $machineId)
    {
        return view('admin.machines.slots.create', compact('machineId'));
    }

    public function deleteSlot(Request $request, $id)
    {
        $machineSlot = MachineSlot::find($id);
        $machineSlot->delete();
        return redirect("admin/machines/slots/$machineSlot->machine_id")
            ->withSuccess('Machine slot deleted successfully');
    }

    public function addSlot(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'machine_id' => 'required',
        ]);
        MachineSlot::create($validated);
        $machineId = $validated['machine_id'];
        return redirect("/seller/machines/slots/$machineId")
            ->withSuccess('Machine slot created successfully');
    }

    public function slotItems(Request $request, $slotId)
    {
        $items = MachineSlotItem::with('user')->where('machine_slot_id', $slotId)->get();
        return view('admin.machines.slots.items.listing', compact('items', 'slotId'));
    }

    public function createSlotItem(Request $request, $slotId)
    {
        return view('admin.machines.slots.items.create', compact('slotId'));
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
        return redirect("/admin/machines/slots/item/" . $validated['machine_slot_id'])
            ->withSuccess('Slot item created successfully');
    }

    public function assign(Request $request, $machineId)
    {
        $assignUser = MachineUser::where('machine_id', $machineId)->pluck('user_id');
        $users = User::where('user_type', 2)->get();
        return view('admin.machines.assign', compact('assignUser', 'users', 'machineId'));
    }

    public function assignUser(Request $request, $machineId)
    {
        MachineUser::where('machine_id', $machineId)->delete();
        if (isset($request->users)) {
            foreach($request->users as $user) {
                MachineUser::create(['machine_id'=> $machineId, 'user_id'=> $user]);
            }
        }
        return redirect("/admin/machines")
        ->withSuccess('Users asigned successfully');
    }
}
