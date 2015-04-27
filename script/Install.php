<?php

namespace CustomLoader;

use Symfony\Component\Yaml\Yaml;

class Install {

  /**
   * The root directory of the application.
   *
   * @var string
   */
  public static $rootDir;

  /***
   * Returns a path by an array of folders/files.
   *
   * @param $directories
   *   The component directories and files.
   * @param bool $relative_to_root
   *   Specifies if it relative to the application root.
   *
   * @return string
   *   The path.
   */
  public static function getPath($directories, $relative_to_root = TRUE) {
    if (empty(Install::$rootDir)) {
      Install::$rootDir = dirname(dirname(dirname(__FILE__)));
    }
    if ($relative_to_root) {
      return implode(
        DIRECTORY_SEPARATOR,
        array_merge(array(Install::$rootDir), $directories)
      );
    }
    else {
      return implode(DIRECTORY_SEPARATOR, $directories);
    }
  }

  /**
   * Creates symlinks for custom code.
   */
  public static function symlinks() {
    $symlink_settings = Install::getPath(array('composer', 'symlink_settings.yml'));
    $custom = Yaml::parse(file_get_contents($symlink_settings));

    foreach ($custom as $type => $link) {
      // Creates folder if it doesn't exist.
      if (!file_exists($type)) {
        mkdir($type, 0777, TRUE);
      }
      if (!file_exists(Install::getPath(array('web', $link)))) {
        mkdir(Install::getPath(array('web', $link)), 0777, TRUE);
      }
      // Creates symlinks.
      Install::_symlink(Install::getPath(array('composer', $type)), Install::getPath(array('web', $link)));
    }
  }

  /**
   * Symlink script.
   *
   * @param $target
   *   The symlink target.
   * @param $link
   *   The symlink link.
   */
  public static function _symlink($target ,$link) {
    if (file_exists($target)) {
      if (!file_exists($link)) {
        mkdir($link, 0777, TRUE);
      }
      exec('ln -fs ' . $target . '/* ' . $link);
    }
  }

  /**
   * Creates and adds symlinks for the shared directory.
   */
  public static function shared() {
    // Creates shared directory.
    $sharedDir = Install::getPath(array('shared'));
    echo $sharedDir;
    if (!file_exists($sharedDir)) {
      mkdir($sharedDir, 0777, TRUE);
    }
    // Create files directory.
    if (!file_exists(Install::getPath(array('shared', 'files')))) {
      mkdir(Install::getPath(array('shared', 'files')), 0777, TRUE);
      // For some reason wrong permissions are added at mkdir.
      chmod(Install::getPath(array('shared', 'files')), 0777);
    }

    // Creates settings.php.
    $settingsConf = Install::getPath(array('web', 'sites', 'default', 'default.settings.php'));
    copy($settingsConf, Install::getPath(array('shared' , 'settings.php')));
    chmod(Install::getPath(array('shared' , 'settings.php')), 0666);
    // Creates services.yml
    $serviceConf = Install::getPath(array('web', 'sites', 'default', 'default.services.yml'));
    copy($serviceConf, Install::getPath(array('shared' , 'services.yml')));
    chmod(Install::getPath(array('shared' , 'services.yml')), 0666);
    // Creates the symlinks.
    Install::_symlink($sharedDir, Install::getPath(array('web', 'sites', 'default')));
  }

}