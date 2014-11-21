<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Player extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $guarded = array('id');

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'players';

    public function ratings() {
        return $this->hasMany('Rating');
    }

    public function getUrlAttribute() {
        return URL::route('players.show', $this->id);
    }

    public function rate($attribute = null) {
    	return $attribute
    		? $this
    			->ratings()
    			->whereAttribute($attribute)
    			->avg('value')
    		: $this
    			->ratings()
    			->avg('value');
    }

    public function ratePerGame($attribute = null, $game_id) {
    	return $attribute
    		? $this
    			->ratings()
    			->whereAttribute($attribute)
    			->whereGameId($game_id)
    			->avg('value')
    		: $this
    			->ratings()
    			->whereGameId($game_id)
    			->avg('value');
    }

    public function getRatedAttributes() {
    	return $this
    		->ratings()
    		->distinct('attribute')
    		->orderBy('attribute')
    		->get(['attribute']);
    }

    public function getRatedAttributesAsArray() {
    	$atts = $this->getRatedAttributes();
    	$attributesArray = [];
    	foreach ($atts as $att) {
    		$attributesArray[] = $att->attribute;
    	}

    	return $attributesArray;
    }

    public function getRatingSummary() {
    	$ratedAttributes = $this->getRatedAttributesAsArray();

		$averages = [];
		foreach ($ratedAttributes as $attribute) {
			$averages[$attribute] = $this->rate($attribute);
		}

		return $averages;
    }


	public static function search($searchQuery) {

        $criteria = explode('+', $searchQuery);		
		
		$query = Player::orderBy('name');

		foreach($criteria as $criterion)
		{	
			$query->orWhere('name', 'LIKE', '%' . $criterion .'%');
		}    
        return $query->paginate(5);
	}
}
