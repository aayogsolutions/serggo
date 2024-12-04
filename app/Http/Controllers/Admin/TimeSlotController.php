<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceTimeSlot;
use Illuminate\Http\Request;
use App\Models\TimeSlot;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TimeSlotController extends Controller
{
    public function __construct(
        private TimeSlot $timeSlot,
        private ServiceTimeSlot $servicetimeSlot
    ){}

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $timeSlots = $this->timeSlot->orderBy('start_time', 'asc')->get();
        return view('Admin.views.timeSlot.index', compact('timeSlots'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);
        
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $slots = $this->timeSlot->latest()->get(['start_time', 'end_time']);

        foreach ($slots as $slot) {
            $existStart = date('H:i', strtotime($slot->start_time));
            $existEnd = date('H:i', strtotime($slot->end_time));
            if(($startTime >= $existStart && $startTime <= $existEnd) || ($endTime >= $existStart && $endTime <= $existEnd)) {
                flash()->error(translate('Time slot overlaps with existing timeslot...'));
                return back();
            }
            if(($existStart >= $startTime && $existStart <= $endTime) || ($existEnd >= $startTime && $existEnd <= $endTime)) {
                flash()->error(translate('Time slot overlaps with existing timeslot!!!'));
                return back();
            }
        }
        
        DB::table('time_slots')->insert([
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'date'       => date('Y-m-d'),
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
       
        flash()->success(translate('Time Slot added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $timeSlots = $this->timeSlot->where(['id' => $id])->first();
        return view('Admin.views.timeSlot.edit', compact('timeSlots'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $slots = $this->timeSlot->where('id', '!=', $id)->get(['start_time', 'end_time']);

        foreach ($slots as $slot) {
            $existStart = date('H:i', strtotime($slot->start_time));
            $existEnd = date('H:i', strtotime($slot->end_time));
            if(($startTime >= $existStart && $startTime <= $existEnd) || ($endTime >= $existStart && $endTime <= $existEnd)) {
                flash()->error(translate('Time slot overlaps with existing timeslot...'));
                return back();
            }
            if(($existStart >= $startTime && $existStart <= $endTime) || ($existEnd >= $startTime && $existEnd <= $endTime)) {
                flash()->error(translate('Time slot overlaps with existing timeslot!!!'));
                return back();
            }
        }

        DB::table('time_slots')->where(['id' => $id])->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'date'       => date('Y-m-d'),
            'status'     => 1,
            'updated_at' => now(),
        ]);

        flash()->success(translate('Time Slot updated successfully!'));
        return redirect()->route('admin.business-settings.store.timeSlot.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $timeSlot = $this->timeSlot->find($request->id);
        $timeSlot->status = $request->status;
        $timeSlot->save();
        flash()->success(translate('TimeSlot status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $timeSlot = $this->timeSlot->find($request->id);
        $timeSlot->delete();
        flash()->success(translate('Time Slot removed!'));
        return back();
    }

    // Service Time Slot

    /**
     * @return Application|Factory|View
     */
    public function ServiceIndex(): View|Factory|Application
    {
        $timeSlots = $this->servicetimeSlot->orderBy('priority', 'asc')->get();
        return view('Admin.views.timeSlot.service.index', compact('timeSlots'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function ServiceStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'time' => 'required',
        ]);
        
        if(!$this->servicetimeSlot->where('time', $request->time)->exists()) {
           $time = $this->servicetimeSlot;
           $time->time = $request->time;
           $time->save();

           flash()->success(translate('Time Slot added successfully!'));
           return back();
        }else{
           flash()->error(translate('Time Slot already exists!'));
           return back(); 
        }
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function ServiceEdit($id): View|Factory|Application
    {
        $timeSlots = $this->servicetimeSlot->where(['id' => $id])->first();
        return view('Admin.views.timeSlot.service.edit', compact('timeSlots'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function ServiceUpdate(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'time' => 'required',
        ]);

        $time = $this->servicetimeSlot->find($id);
        $time->time = $request->time;
        $time->save();

        flash()->success(translate('Time Slot updated successfully!'));
        return redirect()->route('admin.business-settings.store.service.timeSlot.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function ServiceStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $timeSlot = $this->servicetimeSlot->find($request->id);
        $timeSlot->status = $request->status;
        $timeSlot->save();
        flash()->success(translate('TimeSlot status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ServicePriority(Request $request): \Illuminate\Http\JsonResponse
    {
        $timeSlot = $this->servicetimeSlot->find($request->id);
        $timeSlot->priority = $request->priority;
        $timeSlot->save();
        flash()->success(translate('TimeSlot priority updated!'));
        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function ServiceDelete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $timeSlot = $this->servicetimeSlot->find($request->id);
        $timeSlot->delete();
        flash()->success(translate('Time Slot removed!'));
        return back();
    }
}
