<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

use Closure;
use muqsit\wilderness\behaviour\defaults\WorldSelector;
use muqsit\wilderness\behaviour\defaults\WorldSelectorRegistry;
use muqsit\wilderness\Loader;
use muqsit\wilderness\math\Random2DCoordinateGenerator;
use muqsit\wilderness\utils\Language;
use muqsit\wilderness\utils\lists\ListInstance;
use muqsit\wilderness\utils\Position2D;
use pocketmine\level\Position;
use pocketmine\Player;

class DefaultWildernessBehaviour extends ConfigurableBehaviour{

	/** @var WorldSelector */
	private $world_selector;

	/** @var Random2DCoordinateGenerator */
	protected $coordinate_generator;

	/** @var ListInstance<string> */
	protected $worlds_list;

	/** @var Language */
	protected $language;

	public function select(Loader $loader) : void{
		parent::select($loader);
		$config = $this->getConfig();

		$selector_type = $config->getNested("world-selection.type");
		$this->world_selector = WorldSelectorRegistry::get($selector_type, $config->getNested("world-selection.type-config.{$selector_type}"));
		$coordinate_range = $config->get("coordinate-ranges");
		$this->coordinate_generator = new Random2DCoordinateGenerator(
			$coordinate_range["minx"], $coordinate_range["maxx"],
			$coordinate_range["minz"], $coordinate_range["maxz"]
		);
		$this->worlds_list = BehaviourHelper::parseGrayList($config->get("worlds"));
		$this->language = BehaviourHelper::parseLanguage($config->get("language"));
	}

	public function generatePosition(Player $player, Closure $callback) : void{
		$world = $this->world_selector->select($player);

		if(!$this->worlds_list->contains($world->getFolderName())){
			$this->language->translateAndSend($player, "on-command-failed-badworld", [
				"{PLAYER}" => $player->getName(),
				"{WORLD}" => $world->getFolderName()
			]);
			$callback(null);
		}else{
			[$x, $z] = $this->coordinate_generator->generate();
			$this->language->translateAndSend($player, "on-command", [
				"{PLAYER}" => $player->getName(),
				"{X}" => (string) $x,
				"{Z}" => (string) $z,
				"{WORLD}" => $world->getFolderName()
			]);

			$callback(new Position2D($x + 0.5, $z + 0.5, $world));
		}
	}

	public function onTeleportFailed(Player $player, int $reason) : void{
		if($reason === BehaviourTeleportFailReason::COMMAND_LOCK){
			$this->language->translateAndSend($player, "on-command-failed-pending", []);
		}
	}

	public function onTeleportSuccess(Player $player, Position $position) : void{
		$this->language->translateAndSend($player, "on-teleport", [
			"{PLAYER}" => $player->getName(),
			"{X}" => (string) $position->getFloorX(),
			"{Y}" => (string) $position->getFloorY(),
			"{Z}" => (string) $position->getFloorZ(),
			"{WORLD}" => $position->getLevelNonNull()->getFolderName()
		]);
	}

	protected function createConfigContent() : string{
		return <<<'EOD'
# Default behaviour file for Wilderness (wilderness:default)

# Sets coordinate ranges /wild should be bounded to.
# range = [min, max] (closed interval)
coordinate-ranges:
  minx: -10000
  maxx: 10000
  minz: -10000
  maxz: 10000

# Specify which/how a world should be selected to teleport in
world-selection:
  # The selection type
  # Available selection types:
  #  self		- pick same world the player is in [default]
  #  specific	- pick a specific world to teleport to
  #  random		- pick a random world from a list of worlds
  type: self
  # Additional configuration required by the type above
  type-config:
    # No additional config needed for self
    self: []
    # Will always teleport player into this world
    specific:
      # The world to teleport in to
      world: world
    # Picks a random world from the list of worlds specified
    random:
      - world
      - world2
      - world3

# Configure which worlds /wilderness should (or should not) work in
worlds:
  # The type of the list. Available types:
  #   blacklist: All worlds listed in "list" are forbidden
  #   whitelist: Only the worlds listed in "list" are permitted
  type: blacklist
  list:
    - "nether" # highest block in the nether is always at y=128, so this plugin won't work properly in the nether

language:
  # You can use "&" to colour your messages.
  # {PLAYER}: The name of the player the message is being sent to

  # Message sent when player executes /wild
  # {X}: The X coordinate the player is going to get teleported to
  # {Z}: The Z coordinate the player is going to get teleported to
  # {WORLD}: The world the player is going to get teleported to
  on-command: "&eTeleporting..."

  # Message sent when player tries to execute /wild while a /wild
  # request is already pending.
  on-command-failed-pending: "&cYou are executing /wild too quickly, try again in some time."

  # Message sent when player executes /wild in a forbidden world
  # {WORLD}: The world the player is in
  on-command-failed-badworld: "&cYou cannot use /wild in {WORLD}!"

  # Message sent when player teleports to a random spot using /wild
  # {X}: The X coordinate the player is going to get teleported to
  # {Y}: The Y coordinate the player is going to get teleported to
  # {Z}: The Z coordinate the player is going to get teleported to
  # {WORLD}: The world the player is going to get teleported to
  on-teleport: ""
EOD;
	}
}