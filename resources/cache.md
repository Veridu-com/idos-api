## Cache index

Always:
1. Tries to fetch Cache key
2. Fails
3. Proceed with cache changes listed below

Mindset:
	Cache key:  Unique identifier for that "value".
	Cache tags: Which group that "value" fits in?

@getAll
	- save keys: 
		"$prefix.all"
	- save tags:
		"$prefix"
	- for each entity 
		- save keys:
			"$prefix.one.$id"
		- save tags:
			"$prefix"
			"$prefix.parent.$parentId"

@find (get One)
	- save keys:
		"$prefix.one.$id"
	- save tags:
		"$prefix"
		"$prefix.parent.$parentId"

@getByParentId
	- save keys:
		"$prefix.parent.all.$id"
	- save tag:
		"$prefix"
		"$prefix.parent.$parentId"
	- delete keys:
		"$prefix.all"
		"$prefix.one.$id"
		"$prefix.parent.all.$parentId"

@create new
@update one
	- save keys:
		"$prefix.one.$id"
	- save tags:
		"$prefix"
		"$prefix.parent.$parentId"
	- delete keys:
		"$prefix.all"
		"$prefix.parent.all.$parentId"

@delete one
	- delete keys:
		"$prefix.all"
		"$prefix.parent.all.$parentId"
		"$prefix.one.$id"

@delete all
	- delete tags:
		"$prefix"
