{
  pkgs,
  config,
  ...
}:
{
  languages.php = {
    enable = true;
    extensions = [ "mongodb" ];
    version = "8.4";
    ini = ''
      display_errors = On
      error_reporting = E_ALL
    '';
    fpm.pools.web = {
      settings = {
        "clear_env" = "no";
        "pm" = "dynamic";
        "pm.max_children" = 5;
        "pm.start_servers" = 2;
        "pm.min_spare_servers" = 1;
        "pm.max_spare_servers" = 5;
      };
    };
  };

  services.caddy = {
    enable = true;
    virtualHosts.":8080" = {
      extraConfig = ''
        root public
        php_fastcgi unix/${config.languages.php.fpm.pools.web.socket}
        file_server
      '';
    };
  };

  services.mongodb = {
    enable = true;
    package = pkgs.mongodb-7_0;
    initDatabaseUsername = "wai_web";
    initDatabasePassword = "w@i_w3b";
  };

  env = {
    DB_URI = "mongodb://localhost:27017/wai";
    DB_USERNAME = "wai_web";
    DB_PASSWORD = "w@i_w3b";
  };
}
