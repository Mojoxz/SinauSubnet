<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    protected $table = 'murid';

    protected $fillable = ['user_id', 'kelas', 'total_poin', 'poin_dicapai_pada'];

    protected function casts(): array
    {
        return [
            'poin_dicapai_pada' => 'datetime',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function riwayatPoin(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiwayatPoin::class);
    }

    public function badges(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'murid_badge')
                    ->withPivot('diperoleh_pada')
                    ->withTimestamps();
    }

    public function progresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Progress::class);
    }

    public function jawabanPraktikums(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JawabanPraktikum::class);
    }

    public function hasilQuizzes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HasilQuiz::class);
    }

    public function feedbacks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    // --- Helper PBL Dinamis ---

    public function getCurrentPblLevel(): int
    {
        $semuaMateri = Materi::where('is_aktif', true)
                             ->orderBy('level')
                             ->orderBy('urutan')
                             ->get();
                             
        $materiPerLevel = $semuaMateri->groupBy('level');
        $progress = $this->progresses; // load relation
        
        $unlockedLevel = 1;
        foreach($materiPerLevel as $lvl => $materis) {
            $semuaSelesai = true;
            foreach($materis as $m) {
                $p = $progress->where('materi_id', $m->id)->first();
                if (!$p || $p->persen_quiz < 100) {
                    $semuaSelesai = false;
                    break;
                }
            }
            if ($semuaSelesai) {
                $unlockedLevel = $lvl + 1;
            } else {
                break;
            }
        }
        return $unlockedLevel;
    }

    public function getPblStateForMateri(int $materiId): string
    {
        $prog = $this->progresses()->where('materi_id', $materiId)->first();
        if (!$prog) return 'materi';
        
        if ($prog->persen_quiz == 100) return 'selesai';
        if ($prog->persen_praktikum == 100) return 'quiz';
        if ($prog->persen_materi == 100) return 'praktikum';
        
        return 'materi';
    }
}
