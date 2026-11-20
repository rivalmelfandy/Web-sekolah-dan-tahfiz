<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Halaman Beranda (Publik).
     */
    public function beranda(): View
    {
        $stats = [
            ['icon' => 'bi-people-fill', 'angka' => '500+', 'label' => 'Siswa Aktif'],
            ['icon' => 'bi-person-workspace', 'angka' => '40+', 'label' => 'Tenaga Pengajar'],
            ['icon' => 'bi-trophy-fill', 'angka' => '100+', 'label' => 'Prestasi'],
        ];

        $misi = [
            'Menerapkan konsep keislaman di dalam keseharian.',
            'Memadupadankan kurikulum dinas dengan nilai-nilai Al-Quran.',
            'Menjadikan peserta didik berguna bagi masyarakat.',
            'Membangun mental pemimpin sejak dini.',
            'Memberikan pendidikan berupa sains dan keterampilan (skill) sebagai persiapan ke jenjang pendidikan yang lebih tinggi.',
            'Menciptakan lingkungan dan suasana sekolah yang agamis.',
            'Mengembangkan seluruh komponen sekolah secara optimal, baik di bidang akademik maupun nonakademik.',
            'Meningkatkan mutu pendidikan yang mengintegrasikan sistem nilai, agama, dan budaya dengan ilmu pengetahuan dan teknologi.',
        ];

        return view('public.beranda', compact('stats', 'misi'));
    }

    /**
     * Halaman Program Akademik (Publik).
     */
    public function akademik(): View
    {
        $programs = [
            [
                'img' => 'https://placehold.co/600x380/e2e8f0/1e293b?text=Tahfiz+%26+Al-Quran',
                'tag' => 'Pilar Utama',
                'judul' => "Tahfiz & Al-Qur'an",
                'deskripsi' => "Program pembentukan karakter Qur'ani melalui tahsin perbaikan bacaan, tasmi' simakan hafalan, mutaba'ah harian, serta bimbingan intensif target minimal 6 Juz.",
            ],
            [
                'img' => 'https://placehold.co/600x380/e2e8f0/1e293b?text=Leadership+%26+Kepemimpinan',
                'tag' => 'Karakter',
                'judul' => 'Leadership & Kepemimpinan',
                'deskripsi' => 'Mencetak calon pemimpin muslim melalui program Leaders\' Camp, Latihan Dasar Kepemimpinan (LDK), kegiatan kepanduan, dan kedisiplinan berakhlak mulia.',
            ],
            [
                'img' => 'https://placehold.co/600x380/e2e8f0/1e293b?text=Bahasa+%26+Public+Speaking',
                'tag' => 'Komunikasi',
                'judul' => 'Bahasa & Public Speaking',
                'deskripsi' => 'Membangun kepercayaan diri dan kecakapan komunikasi global siswa melalui English & Arabic Day, muhadharah pidato dua bahasa, dan festival literasi.',
            ],
            [
                'img' => 'https://placehold.co/600x380/e2e8f0/1e293b?text=Sukses+SNBT+%26+PTN+Favorit',
                'tag' => 'Akademik',
                'judul' => 'Sukses SNBT & PTN Favorit',
                'deskripsi' => 'Mempersiapkan siswa menembus PTN dan Perguruan Tinggi Kedinasan favorit melalui bimbingan intensif UTBK-SNBT, tryout berkala, dan konsultasi minat bakat.',
            ],
            [
                'img' => 'https://placehold.co/600x380/e2e8f0/1e293b?text=Bina+Iman+%26+Taqwa+(BIMTAQ)',
                'tag' => 'Spiritual',
                'judul' => 'Bina Iman & Taqwa (BIMTAQ)',
                'deskripsi' => 'Memperkuat pondasi keimanan dan ketaqwaan siswa melalui program Malam Bina Iman dan Taqwa (MABIT), Taujihad spiritual, dan pembiasaan shalat berjamaah.',
            ],
            [
                'img' => 'https://placehold.co/600x380/e2e8f0/1e293b?text=Sosial+%26+Karakter+Muslim',
                'tag' => 'Sosial',
                'judul' => 'Sosial & Karakter Muslim',
                'deskripsi' => "Membangun kepekaan sosial dan jiwa berbagi sebagai muslim sejati melalui program Jum'at Berkah sedekah mingguan, bakti sosial, dan kepedulian lingkungan hidup.",
            ],
        ];

        $ekskul = [
            ['nama' => 'Muhadhoroh Ikhwan', 'kategori' => 'Keagamaan', 'icon' => 'bi-mic-fill', 'gradient' => 'linear-gradient(135deg, #1F4172 0%, #1e293b 100%)'],
            ['nama' => 'Muhadhoroh Akhwat', 'kategori' => 'Keagamaan', 'icon' => 'bi-chat-heart-fill', 'gradient' => 'linear-gradient(135deg, #059669 0%, #065f46 100%)'],
            ['nama' => 'Pencak Silat Tapak Suci', 'kategori' => 'Bela Diri', 'icon' => 'bi-shield-shaded', 'gradient' => 'linear-gradient(135deg, #DC2626 0%, #991b1b 100%)'],
            ['nama' => 'MTQ & Tilawah', 'kategori' => "Al-Qur'an", 'icon' => 'bi-book-half', 'gradient' => 'linear-gradient(135deg, #0D9488 0%, #115e59 100%)'],
            ['nama' => 'Pramuka Garuda', 'kategori' => 'Kepanduan', 'icon' => 'bi-compass-fill', 'gradient' => 'linear-gradient(135deg, #78350F 0%, #451a03 100%)'],
            ['nama' => 'Paskibra Sekolah', 'kategori' => 'Kedisiplinan', 'icon' => 'bi-flag-fill', 'gradient' => 'linear-gradient(135deg, #B91C1C 0%, #7f1d1d 100%)'],
            ['nama' => 'Sanggar Seni Fadhilah', 'kategori' => 'Seni Islami', 'icon' => 'bi-palette-fill', 'gradient' => 'linear-gradient(135deg, #7C3AED 0%, #5b21b6 100%)'],
            ['nama' => 'Futsal Club', 'kategori' => 'Olahraga', 'icon' => 'bi-trophy-fill', 'gradient' => 'linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%)'],
            ['nama' => 'English & Arabic Club', 'kategori' => 'Bahasa Asing', 'icon' => 'bi-translate', 'gradient' => 'linear-gradient(135deg, #0284C7 0%, #0369a1 100%)'],
            ['nama' => 'Hadroh & Shalawat', 'kategori' => 'Seni Musik', 'icon' => 'bi-music-note-beamed', 'gradient' => 'linear-gradient(135deg, #047857 0%, #064e3b 100%)'],
            ['nama' => 'Klub Badminton', 'kategori' => 'Olahraga', 'icon' => 'bi-dribbble', 'gradient' => 'linear-gradient(135deg, #EA580C 0%, #c2410c 100%)'],
            ['nama' => 'Karya Ilmiah Remaja (KIR)', 'kategori' => 'Sains & Riset', 'icon' => 'bi-lightbulb-fill', 'gradient' => 'linear-gradient(135deg, #4F46E5 0%, #3730a3 100%)'],
        ];

        // Daftar file foto prestasi yang dicari di public/images/prestasi/
        $prestasiCount = 6;

        return view('public.akademik', compact('programs', 'ekskul', 'prestasiCount'));
    }

    /**
     * Halaman SPMB – Penerimaan Peserta Didik Baru (Publik).
     */
    public function spmb(): View
    {
        $langkah = [
            [
                'no' => 1,
                'judul' => 'Pengambilan Formulir',
                'deskripsi' => 'Ambil formulir pendaftaran secara langsung di Kantor Tata Usaha SMA IT Al-Fadhilah atau unduh dari website resmi sekolah pada hari dan jam kerja.',
                'icon' => 'bi-file-earmark-arrow-down-fill',
                'warna' => '#2563EB',
            ],
            [
                'no' => 2,
                'judul' => 'Pengisian & Penyerahan Formulir',
                'deskripsi' => 'Isi formulir dengan lengkap dan benar, lampirkan seluruh berkas persyaratan yang telah ditentukan, kemudian serahkan ke Panitia SPMB.',
                'icon' => 'bi-pencil-fill',
                'warna' => '#0369A1',
            ],
            [
                'no' => 3,
                'judul' => 'Seleksi Administrasi',
                'deskripsi' => 'Panitia akan memverifikasi kelengkapan dan keabsahan dokumen yang diserahkan. Calon siswa akan dihubungi jika terdapat dokumen yang perlu dilengkapi.',
                'icon' => 'bi-search',
                'warna' => '#7C3AED',
            ],
            [
                'no' => 4,
                'judul' => 'Tes Seleksi & Wawancara',
                'deskripsi' => 'Calon siswa yang lolos seleksi administrasi akan mengikuti tes tertulis (Matematika, Bahasa Indonesia, Tahfiz) dan wawancara bersama orang tua/wali.',
                'icon' => 'bi-clipboard2-check-fill',
                'warna' => '#16A34A',
            ],
            [
                'no' => 5,
                'judul' => 'Pengumuman & Daftar Ulang',
                'deskripsi' => 'Hasil seleksi diumumkan melalui website dan papan pengumuman sekolah. Calon siswa yang dinyatakan lulus wajib melakukan daftar ulang sesuai jadwal.',
                'icon' => 'bi-check-circle-fill',
                'warna' => '#1F4172',
            ],
        ];

        $syarat = [
            [
                'no' => 1,
                'judul' => 'Mengisi Formulir Pendaftaran',
                'detail' => 'Mengisi formulir pendaftaran calon murid baru secara lengkap dan valid.',
                'icon' => 'bi-pencil-square',
            ],
            [
                'no' => 2,
                'judul' => 'Surat Keterangan Lulus (Menyusul jika belum ada)',
                'detail' => 'Surat Keterangan Lulus (SKL) resmi dari SMP/MTs sederajat asal siswa.',
                'icon' => 'bi-file-earmark-check-fill',
            ],
            [
                'no' => 3,
                'judul' => 'Fotokopi Rapor Kelas IX SMP/MTs (1 Rangkap)',
                'detail' => 'Fotokopi buku rapor kelas IX semester ganjil/genap yang telah dilegalisir.',
                'icon' => 'bi-journal-bookmark-fill',
            ],
            [
                'no' => 4,
                'judul' => 'Fotokopi Akta Kelahiran dan Kartu Keluarga (KK) (Masing-masing 2 lembar)',
                'detail' => 'Fotokopi akta kelahiran dan kartu keluarga yang masih berlaku dan terbaca jelas.',
                'icon' => 'bi-person-vcard-fill',
            ],
            [
                'no' => 5,
                'judul' => 'Fotokopi KTP Kedua Orang Tua (Masing-masing 2 lembar)',
                'detail' => 'Fotokopi KTP ayah dan ibu atau wali calon peserta didik baru.',
                'icon' => 'bi-card-heading',
            ],
            [
                'no' => 6,
                'judul' => 'Fotokopi Kartu Indonesia Pintar (KIP) (Jika ada)',
                'detail' => 'Bagi calon siswa yang memiliki kartu program bantuan pendidikan KIP dari pemerintah.',
                'icon' => 'bi-award-fill',
            ],
        ];

        $biayaPendidikan = [
            [
                'no' => 1,
                'nama' => 'Pembangunan',
                'laki' => 'Rp 1.500.000',
                'perempuan' => 'Rp 1.500.000',
                'catatan_p' => '',
                'keterangan' => 'Sekali Bayar',
            ],
            [
                'no' => 2,
                'nama' => 'Seragam 5 Stel',
                'laki' => 'Rp 1.550.000',
                'perempuan' => 'Rp 2.000.000',
                'catatan_p' => 'Plus 6 Jilbab',
                'keterangan' => 'Sekali Bayar',
            ],
            [
                'no' => 3,
                'nama' => 'SPP',
                'laki' => 'Rp 300.000',
                'perempuan' => 'Rp 300.000',
                'catatan_p' => '',
                'keterangan' => 'Per Bulan',
            ],
            [
                'no' => 4,
                'nama' => 'Ekstrakurikuler',
                'laki' => 'Rp 500.000',
                'perempuan' => 'Rp 500.000',
                'catatan_p' => '',
                'keterangan' => 'Per Semester',
            ],
            [
                'no' => 5,
                'nama' => 'Formulir',
                'laki' => 'Rp 150.000',
                'perempuan' => 'Rp 150.000',
                'catatan_p' => '',
                'keterangan' => 'Sekali Bayar',
            ],
        ];

        return view('public.spmb', compact('langkah', 'syarat', 'biayaPendidikan'));
    }
}
