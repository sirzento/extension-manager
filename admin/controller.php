<?php

namespace Pterodactyl\Http\Controllers\Admin\Extensions\{identifier};

use Illuminate\View\View;
use Illuminate\View\Factory as ViewFactory;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Pterodactyl\Contracts\Repository\SettingsRepositoryInterface;
use Pterodactyl\Http\Requests\Admin\AdminFormRequest;
use Illuminate\Http\RedirectResponse;

// https://blueprint.zip/docs/?page=documentation/$blueprint
use Pterodactyl\BlueprintFramework\Libraries\ExtensionLibrary\Admin\BlueprintAdminLibrary as BlueprintExtensionLibrary;

class {identifier}ExtensionController extends Controller
{
  public function __construct(
    private ViewFactory $view,
    private BlueprintExtensionLibrary $blueprint,
    private ConfigRepository $config,
    private SettingsRepositoryInterface $settings,
  ) {}
  
  public function index(): View
  {
    $user = $this->blueprint->dbGet('{identifier}', 'user');
    $port = $this->blueprint->dbGet('{identifier}', 'port');
    $useSSHKey = $this->blueprint->dbGet('{identifier}', 'useSSHKey');

    return $this->view->make(
      'admin.extensions.{identifier}.index', [
        'root' => "/admin/extensions/{identifier}",
        'blueprint' => $this->blueprint,
        'user' => $user,
        'port' => $port,
        'useSSHKey' => $useSSHKey,
      ]
    );
  }

  public function update({identifier}SettingsFormRequest $request): RedirectResponse
  {
      $request['useSSHKey'] = $request->has('useSSHKey');
      foreach ($request->normalize() as $key => $value) {
          $this->blueprint->dbSet("{identifier}", $key, $value);
      }

      return redirect()->route('admin.extensions.{identifier}.index');
  }
}

class {identifier}SettingsFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'user' => ['string'],
            'port' => ['numeric', 'gt:0'],
            'useSSHKey' => ['sometimes'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user' => 'Username',
            'port' => 'SSH Port',
            'useSSHKey' => 'use SSH key',
        ];
    }
}

