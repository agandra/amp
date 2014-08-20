AMP functionality
============

This is a plugin to help me structure projects and stop me from rebuilding the same core elements for every project.  The aim of this project is to extend some of the core laravel functionality, and generate boilerplate to jumpstart projects.

#### Composer Install

    "require": {
        "agandra/amp": "dev-master"
    }

#### Import config setting and run migrations

	php artisan config:publish agandra/amp

#### Include Service Provider

	\Agandra\Amp\Providers\AmpServiceProvider

### AMP Philosophy

This package should be primarily used for large packages where the standard Laravel structure does not provide enough organization.  Projects will be broken down into 3 main components: Models, Services, and Repos.  Each component serves a unique purpose.

#### Models

These are the standard model files that inherit from \Eloquent.  Each instance of a model represents one row from the table.  We do not use these model files to build queries to get results, in controllers.  We do not create instances of the Models in the controller files, we should only be manipulating these model files in Services and Repos.

#### Repos

Repos are used to collect data from your datasources.  Repos can be used to combine multiple models and obtain relevent datasets.  The idea behind calling a Repo to collect data instead of just doing Model::get() is to be able to filter data sets without having to change calls throughout the code.  

Usually you gather data from the Repo by calling the query Method: Repo->query($params);  In the params you pass an array of attributes you want the dataset to return.  Query method will return a DB builder instance.

Repo's also have other helpers such as find, and findOrFail.

For instance, say you are developing an Item Model, for users to purchase items.  And throughout the code you have multiple calls to $item->get(); to return the relevent items.  Now lets say you add an attribute to the items table 'purchasable'.  If this field is set to true, only then do you want the relevent rows to be returned.  Instead of going everywhere through the code and adding $item->where('purchasable','=',true)->get(), you can add one line in your query method to change what is returned on every repo call.

This is a very basic example and real world usages will get much more complex.  But it is convenient to have one method where you gather your dataset.

#### Services

Services are repeatable actions to manipulate data.  Services can consist of actions such as sending emails to users, or creating models, editing models, or deleting.  Usually services pertain to actions or manipulating data.  


### AMP Validator

The AMP validator works by setting the rules and customMessages array in a model.  Then we pass the class and input to the amp validator instance.  The advantage of the AMP validator is the use of multiple contexts.  Instead of a one dimensional array for rules, it can now be multi dimensional to allow for different rulesets to be allowed in different circumstances. 

Example rules attribute:

	$rules = [
				'create'=>
					['name'=>'required'],
				'edit'=>
					['price'=>'required','value'=>'numeric'],
				'other_context'=>
					['name'=>'required']
			];

The validator will only use the context specified when created.  For instance:

	$validator = \Amp::validator($input, new User);
	$validator->addContext('create');
	if($validator->fails())
		throw new Exception;

You can also add multiple contexts to one validator, and it will merge the rulesets and validate all of them.


### AMP Model

The AMP Model comes with an auto save feature to make saving user input easy.  It will automatically use the AMP Validator and pass the correct context.

	if($user->autoSave()) {
		// Passes the save
	} else {
		// Save has failed
		throw new Exception($user->messages()); 
	}

If the autosave fails, then there will be validation errors, that you can obtain by calling the messages method of the model.  

The AMP model also has hooks to call before and after saving, and validation.  You can also set the autoHash attribute on the model and it will hash the value before saving to the database.  The saving is also done in a DB Transaction, so if any errors or thrown or the before or after save generate errors, the entire database will remain unchanged.

To use the AMP Model, make your model file extend \Agandra\Amp\Base\AmpModel

### AMP Reply

### API

### AMP

The main AMP class is just a helper to access the different attributes available through the AMP project.  For instance to access a repo just call 
	
	\Amp::repo('RepoName')->query();

To get an instance of the validator just call

	\Amp::validator($input, $class);

Of course these elements are available without calling AMP but are made easier through this class.

