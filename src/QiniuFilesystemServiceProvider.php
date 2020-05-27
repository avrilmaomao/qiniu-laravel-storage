<?php namespace pmcaff\QiniuStorage;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use pmcaff\QiniuStorage\Plugins\DownloadUrl;
use pmcaff\QiniuStorage\Plugins\Fetch;
use pmcaff\QiniuStorage\Plugins\ImageExif;
use pmcaff\QiniuStorage\Plugins\ImageInfo;
use pmcaff\QiniuStorage\Plugins\AvInfo;
use pmcaff\QiniuStorage\Plugins\ImagePreviewUrl;
use pmcaff\QiniuStorage\Plugins\LastReturn;
use pmcaff\QiniuStorage\Plugins\PersistentFop;
use pmcaff\QiniuStorage\Plugins\PersistentStatus;
use pmcaff\QiniuStorage\Plugins\PrivateDownloadUrl;
use pmcaff\QiniuStorage\Plugins\Qetag;
use pmcaff\QiniuStorage\Plugins\UploadToken;
use pmcaff\QiniuStorage\Plugins\PrivateImagePreviewUrl;
use pmcaff\QiniuStorage\Plugins\VerifyCallback;
use pmcaff\QiniuStorage\Plugins\WithUploadToken;

class QiniuFilesystemServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Storage::extend(
            'qiniu',
            function ($app, $config) {
                if (isset($config['domains'])) {
                    $domains = $config['domains'];
                } else {
                    $domains = [
                        'default' => $config['domain'],
                        'https'   => null,
                        'custom'  => null
                    ];
                }
                $qiniu_adapter = new QiniuAdapter(
                    $config['access_key'],
                    $config['secret_key'],
                    $config['bucket'],
                    $domains,
                    isset($config['notify_url']) ? $config['notify_url'] : null,
                    isset($config['access']) ? $config['access'] : 'public',
                    isset($config['hotlink_prevention_key']) ? $config['hotlink_prevention_key'] : null
                );
                $file_system = new Filesystem($qiniu_adapter);
                $file_system->addPlugin(new PrivateDownloadUrl());
                $file_system->addPlugin(new DownloadUrl());
                $file_system->addPlugin(new AvInfo());
                $file_system->addPlugin(new ImageInfo());
                $file_system->addPlugin(new ImageExif());
                $file_system->addPlugin(new ImagePreviewUrl());
                $file_system->addPlugin(new PersistentFop());
                $file_system->addPlugin(new PersistentStatus());
                $file_system->addPlugin(new UploadToken());
                $file_system->addPlugin(new PrivateImagePreviewUrl());
                $file_system->addPlugin(new VerifyCallback());
                $file_system->addPlugin(new Fetch());
                $file_system->addPlugin(new Qetag());
                $file_system->addPlugin(new WithUploadToken());
                $file_system->addPlugin(new LastReturn());

                return $file_system;
            }
        );
    }

    public function register()
    {
        //
    }
}
