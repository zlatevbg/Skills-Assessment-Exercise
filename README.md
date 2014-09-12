# Skills Assessment Exercise: PHP, CSS, JavaScript, SQL.

Please complete the following exercise. Please turn in all files including _SQL Import File_ or _SQL create statement_. We will be scoring several aspects of the exercise, some of the most important ones are:

* PHP, jQuery, MySQL knowledge
* Database Structure
* Javascript Utilization
* PHP Security Measures
* Accurate and Uncluttered / Organized coding
* Organization of Files and Folders

## PHP

1. Organize the following list of items into an array variable:
	* Fruit: Apple
	* Color: Yellow
	* Number: 7
2. Modify the 3rd element from the above array by multiplying the value 7 to equal 21 using an equation.
3. Convert the array variable created above into JSON and save into a new variable.
4. Decode the above JSON variable back to a normal array and echo to browser using a foreach loop in an unordered list.
5. Place a condition inside the foreach loop so that the value in the 2nd element is displayed in lowercase.

## CSS

Style the above unordered list as per the following instructions:

1. List Style None
2. Display each list item as block
3. Give each list item a background Color
4. Change background color on Hover
5. Add a 20px margin between each list item
6. Center text

## jQuery

1. Upon clicking the first list item replace the word “__Apple__” with “__Orange__”
2. Upon hovering the mouse over the second list item change its background color to some other color.
3. Upon clicking the third list item have it fade away and reappear above the first item.

## MySQL

Create a database with the following:

1. 4 Columns
	* id
	* label
	* value
	* timestamp (default value = current_timestamp)
2. Add a link below the unordered list that would save the list items into the database as separate rows in the order in which they are displayed - using jQuery Ajax.

	Example:
| ID | Label | Value  |
| -- | ----- | -----  |
| 1 | Fruit  | Orange |
| 2 | Color  | Yellow |
| 3 | Number | 7      |