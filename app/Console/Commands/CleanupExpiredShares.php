<?php

namespace App\Console\Commands;

use App\Models\Share;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredShares extends Command
{
    protected $signature = 'shares:cleanup';
    protected $description = 'Clean up expired shares and their files';

    public function handle()
    {
        $this->info('Starting share cleanup...');
        
        $expiredShares = Share::where('expires_at', '<=', now())->get();
        
        $count = $expiredShares->count();
        
        foreach ($expiredShares as $share) {
            if ($share->type === 'file') {
                Storage::disk('public')->deleteDirectory('shares/'.$share->share_id);
            }
            $share->delete();
        }
        
        $this->info("Cleaned up {$count} expired shares.");
        
        return 0;
    }
}