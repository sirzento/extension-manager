<?php

namespace Pterodactyl\BlueprintFramework\Extensions\{identifier};

use Pterodactyl\Http\Requests\Admin\AdminFormRequest;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\BlueprintFramework\Libraries\ExtensionLibrary\Admin\BlueprintAdminLibrary as BlueprintExtensionLibrary;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;
use Illuminate\Http\Request;

use Alert;

class ExtensionDashboardController extends Controller
{
    public function __construct(
        private BlueprintExtensionLibrary $blueprint,
    ) {}

    public function deleteExtension({identifier}DeleteFormRequest $request)
    {
        try {
            // SSH - Delete extension
            $username = $this->blueprint->dbGet('{identifier}', 'user');
            $port = $this->blueprint->dbGet('{identifier}', 'port');
            $useSSHKey = $this->blueprint->dbGet('{identifier}', 'useSSHKey');

            $auth;
            if ($useSSHKey) {
                if (!$request->file('sshkey')) {
                    Alert::danger('Error - No SSH key provided.')->flash();
                    return redirect()->back();
                }
                $auth = PublicKeyLoader::load($request->file('sshkey')->get());
            } else {
                if (!$request['password']) {
                    Alert::danger('Error - No password provided.')->flash();
                    return redirect()->back();
                }
                $auth = $request['password'];
            }

            $ssh = new SSH2('127.0.0.1', $port);
            if(!$ssh->login($username, $auth)){
                Alert::danger('Error - Username, password or SSH key wrong.')->flash();
                return redirect()->back();
            }

            $canSudoWithoutPassword = $ssh->exec('sudo -n true');
            $sudoRequiresPassword = strpos($canSudoWithoutPassword, 'password') !== false || $ssh->getExitStatus() !== 0;

            $ssh->write("sudo -k /usr/local/bin/blueprint -r " . $request['identifier'] . "\n");
            if ($sudoRequiresPassword) {
                $ssh->read('Password:');
                $ssh->write($request['password'] . "\n");
            }
            $ssh->read('(y/N)');
            $ssh->write("y\n");
            $ssh->read('has been removed');

            return redirect()->route('admin.extensions.{identifier}.index');
        } catch (\Exception $e) {
            Alert::danger('Error - Can\'t connect via SSH (Disabled or wrong port?): ' . $e->getMessage())->flash();
            return redirect()->back();
        }
    }

    public function upload({identifier}InstallFormRequest $request)
    {
        try {
            // Upload file
            $filename = $request->file('file')->getClientOriginalName();
            $request->file('file')->move('{root}', $filename);

            // SSH - Install extension
            $username = $this->blueprint->dbGet('{identifier}', 'user');
            $port = $this->blueprint->dbGet('{identifier}', 'port');
            $useSSHKey = $this->blueprint->dbGet('{identifier}', 'useSSHKey');

            $auth;
            if ($useSSHKey) {
                if (!$request->file('sshkey')) {
                    Alert::danger('Error - No SSH key provided.')->flash();
                    return redirect()->back();
                }
                $auth = PublicKeyLoader::load($request->file('sshkey')->get());
            } else {
                if (!$request['password']) {
                    Alert::danger('Error - No password provided.')->flash();
                    return redirect()->back();
                }
                $auth = $request['password'];
            }

            $ssh = new SSH2('127.0.0.1', $port);
            if(!$ssh->login($username, $auth)){
                Alert::danger('Error - Username or password wrong or SSH is disabled.')->flash();
                return redirect()->back();
            }

            $canSudoWithoutPassword = $ssh->exec('sudo -n true');
            $sudoRequiresPassword = strpos($canSudoWithoutPassword, 'password') !== false || $ssh->getExitStatus() !== 0;

            $ssh->write("sudo -k /usr/local/bin/blueprint -i " . $filename . "\n");
            if ($sudoRequiresPassword) {
                $ssh->read('Password:');
                $ssh->write($request['password'] . "\n");
            }
            $ssh->read('has been installed');

            return redirect()->route('admin.extensions.{identifier}.index');
        } catch (\Exception $e) {
            Alert::danger('Error - Can\'t connect via SSH (Disabled or wrong port?): ' . $e->getMessage())->flash();
            return redirect()->back();
        }
    }
}

class {identifier}InstallFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if ($ext !== 'blueprint') {
                        $fail('Only \'.blueprint\' files can be installed.');
                    }
                }
            ],
            'sshkey' => 'sometimes|file',
            'password' => 'sometimes|string|nullable'
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'File',
            'password' => 'Password',
            'sshkey' => 'SSH key',
        ];
    }
}

class {identifier}DeleteFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['sometimes', 'string'],
            'sshkey' => ['sometimes', 'file'],
            'identifier' => ['required', 'string', 'alpha_num'],
        ];
    }

    public function attributes(): array
    {
        return [
            'password' => 'Password',
            'identifier' => 'Identifier',
            'sshkey' => 'SSH key',
        ];
    }
}