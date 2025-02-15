<?php

namespace App\Observers;

use App\Models\JamPelajaran;

class JamPelajaranObserver
{
    private function updateNomor()
    {
        $jampels = JamPelajaran::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), jam_mulai ASC")->get();
        $counter = 1;
        $previousDay = null;
        $calendarStartTime = "00:00";
        $currentCalendarTime = 0; // Total minutes for calendar time

        $jampels->each(function ($jampel) use (&$counter, &$previousDay, &$calendarStartTime, &$currentCalendarTime) {
            // Jika hari berbeda dari hari sebelumnya, reset counter dan waktu kalender
            if ($previousDay !== $jampel->hari) {
                $counter = 1;
                $currentCalendarTime = 0; // Reset calendar time
            }

            // Tentukan nomor berdasarkan kondisi
            if (is_null($jampel->event) || $jampel->event == "Upacara") {
                $jampel->nomor = $counter++;
            } else {
                $jampel->nomor = null;
            }

            // Hitung durasi jampel (dalam menit)
            $jamMulai = strtotime($jampel->jam_mulai);
            $jamSelesai = strtotime($jampel->jam_selesai);
            $duration = ($jamSelesai - $jamMulai) / 60; // Durasi dalam menit

            // Hitung jam_mulai_calendar dan jam_selesai_calendar
            $jamMulaiCalendar = $this->minutesToTime($currentCalendarTime);

            if (strpos($jampel->event, "Upacara") !== false) {
                $currentCalendarTime += 180; // 3 jam untuk event yang mengandung "Upacara"
            } else {
                $currentCalendarTime += is_null($jampel->event) ? 120 : 60; // Tambahkan waktu kalender (2 jam untuk durasi > 30 menit, 1 jam untuk durasi <= 30 menit)
            }

            $jamSelesaiCalendar = $this->minutesToTime($currentCalendarTime);

            $jampel->jam_mulai_calendar = $jamMulaiCalendar;
            $jampel->jam_selesai_calendar = $jamSelesaiCalendar;

            $jampel->saveQuietly();

            // Set hari sebelumnya untuk perbandingan di iterasi berikutnya
            $previousDay = $jampel->hari;
        });
    }

    private function minutesToTime($minutes)
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
    
    public function creating(JamPelajaran $jamPelajaran)
    {
        // Logika sebelum membuat data baru
    }

    public function created(JamPelajaran $jamPelajaran)
    {
        $this->updateNomor();
    }

    public function updating(JamPelajaran $jamPelajaran)
    {
        // Logika sebelum memperbarui data
    }

    public function updated(JamPelajaran $jamPelajaran)
    {
        $this->updateNomor();
    }

    public function deleting(JamPelajaran $jamPelajaran)
    {
        // Logika sebelum menghapus data
    }

    public function deleted(JamPelajaran $jamPelajaran)
    {
        $this->updateNomor();
    }

    public function restoring(JamPelajaran $jamPelajaran)
    {
        // Logika sebelum memulihkan data (soft delete)
    }

    public function restored(JamPelajaran $jamPelajaran)
    {
        $this->updateNomor();
    }

    public function saving(JamPelajaran $jamPelajaran)
    {
        // Logika sebelum menyimpan data (create/update)
    }

    public function saved(JamPelajaran $jamPelajaran)
    {
        $this->updateNomor();
    }
}
