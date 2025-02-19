/* based on https://github.com/mneofit/multiselect
   edited	 */

if (!m_helper)
{
	var m_helper = {
		removeNode : function(id) {
			var el = document.getElementById(id);
			if (el) {
				el.parentNode.removeChild(el);
			}
		},
		
		insertAfter : function(item, target) {
			var parent = target.parentNode;
			if (target.nextElementSibling) {
				parent.insertBefore(item, target.nextElementSibling);
			} else {
				parent.appendChild(item);
			}
		},
		
		hide : function(element) {
			element.style.display = 'none';
		},
		
		hideAll : function(array) {
			for(var i = 0; i< array.length; i++) {
				this.hide(array[i]);
			}
		},		
		
		show : function(element) {
			element.style.display = 'block';
		},
		
		showAll : function(array) {
			for(var i = 0; i< array.length; i++) {
				this.show(array[i]);
			}
		},
		
		parent : function(element, id) {
			var parent = element.parentElement;
			while (parent && parent.tagName != 'BODY') {
				if (parent.id == id) {
					return parent;
				}

				parent = parent.parentElement;
			}
			
			return null;
		},
		
		//data : { tag, id, class, attributes : { key : value }, data : { key : value } }
		create : function(data) {
			var result = document.createElement(data.tag);
			if (data.id) {
				result.id = data.id;				
			}
			
			if (data.class) {
				result.className = data.class;
			}
			
			if (data.attributes) {
				for(var prop in data.attributes) {
					result.setAttribute(prop, data.attributes[prop]);
				}
			}
			
			if (data.data) {
				for(var prop in data.data) {
					result.dataset[prop] = data.data[prop];
				}
			}
			
			return result;
		},
		
		div : function(data) {
			if (!data) {
				data = new Object();
			}
			
			data.tag = 'div';
			return this.create(data);
		},
		
		label : function(data) {
			if (!data) {
				data = new Object();
			}
			
			data.tag = 'label';
			return this.create(data);
		},
		
		textField : function(data) {
			if (!data) {
				data = new Object();
			}
			
			data.tag = 'input';
			if (!data.attributes)
				data.attributes = new Object();
			
			data.attributes.type = 'text';
			
			return this.create(data);
		},
		
		checkbox : function(data) {
			if (!data) {
				data = new Object();
			}
			
			data.tag = 'input';
			if (!data.attributes)
				data.attributes = new Object();
			
			data.attributes.type = 'checkbox';
			
			return this.create(data);
		},
		
		each : function(array, handler) {
			for(var i = 0; i< array.length; i++) {
				handler(array[i]);
			}
		},
		
		setActive : function(element) {
			element.classList.add('multiselect-active');
		},
		
		setUnactive : function(element) {
			element.classList.remove('multiselect-active');
		},
		
		select :function (element) {
			element.selected = true;
			element.setAttribute('selected', 'selected');
		},
		
		deselect : function (element) {
			element.selected = false;
			element.removeAttribute('selected');
		},
		
		check : function(element) {
			element.checked = true;
		},
		
		uncheck : function(element) {
			element.checked = false;
		},
		
		click : function(element) {
			if (element.fireEvent) {
				el.fireEvent('onclick');
			} else {
				var evObj = document.createEvent('Events');
				evObj.initEvent('click', true, false);
				element.dispatchEvent(evObj);
			}
		},
		setDisabled: function(element, value) {
			element.disabled = value;
		},
	};
}

function Multiselect(item, opts) {
	//if item is not a select - it is an error
	if ((typeof($) != 'undefined' && !$(item).is('select')) ||
		(typeof($) == 'undefined' && item.tagName != 'SELECT')) {
		throw "Multiselect: passed object must be a select";
	}
	
	if ((typeof($) != 'undefined' && !$(item).attr('multiple')) ||
		(typeof($) == 'undefined' && !item.hasAttribute('multiple'))) {
		throw "Multiselect: passed object should contain 'multiple' attribute";	
	}

	this._item = item;

	this._createUI();

	this._appendEvents();

	this._initSelectedFields();

	this._initIsEnabled();
}

Multiselect.prototype = {
	//creates representation
	_createUI: function () {
		m_helper.removeNode(this._getIdentifier());

		var wrapper = this._createWrapper();
		
		m_helper.insertAfter(wrapper, this._item);

		wrapper.appendChild(this._createInputField());
		wrapper.appendChild(this._createItemList());
		
		m_helper.hide(this._item);
	},

	//creates base wrapper for control
	_createWrapper: function () {
		var result = document.createElement('div');
		result.className = 'multiselect-wrapper';
		result.id = this._getIdentifier();
		return result;
	},

	//creates input field
	_createInputField: function () {
		var input = m_helper.textField({
			id : this._getInputFieldIdentifier(),
			class : 'multiselect-input',
			attributes : {
				autocomplete: 'off',
				readonly: 'readonly'
			}
		}),
		label = m_helper.label({
			id : this._getInputBadgeIdentifier(),
			class : 'multiselect-count',
			attributes : {
				for : this._getInputFieldIdentifier()
			}
		}),
		dropDownArrow = m_helper.label({
			class : 'multiselect-dropdown-arrow',
			attributes : {
				for : this._getInputFieldIdentifier()
			}
		}),
		result = m_helper.div({
			class : 'multiselect-input-div'
		});

		label.style.visibility = 'hidden';
		label.innerHTML = 0;
		input.placeholder= 'skupiny';
		
		result.appendChild(input);
		result.appendChild(label);
		result.appendChild(dropDownArrow);

		return result;
	},
	//creates element list
	_createItemList: function () {
		var list = m_helper.create({ tag : 'ul'});

		var self = this;
		m_helper.each(this._getItems(this._item), function(e) {
			var insertItem = self._createItem('li', e.id, e.text, e.selected);
			list.appendChild(insertItem);

			var checkBox = insertItem.querySelector('input[type=checkbox]');
			e.multiselectElement = checkBox;
			checkBox.dataset.multiselectElement = JSON.stringify(e);
		});

		var result = m_helper.div({
			id : this._getItemListIdentifier(),
			class : 'multiselect-list'
		});
		result.appendChild(list);

		return result;
	},

	//creates single list element
	_createItem: function (wrapper, value, text, selected) {
		var checkBox = m_helper.checkbox({
			class : 'multiselect-checkbox',
			data : {
				val : value
			}
		}),
		textBox = m_helper.create({ tag : 'span', class : 'multiselect-text'}),
		result = m_helper.create({ tag: wrapper }),
		label = m_helper.label();
		
		textBox.className = 'multiselect-text';
		textBox.innerHTML = text;

		label.appendChild(checkBox);
		label.appendChild(textBox);
		
		result.appendChild(label);
		return result;
	},

	_initSelectedFields: function () {
		var itemResult = this._getItems().filter(function (obj) {
			return obj.selected;
		});

		if (itemResult.length != 0) {
			var self = this;
			m_helper.each(itemResult, function(e) {
				self.select(e.id);
			});
		}
		
		this._hideList(this);
	},

	_initIsEnabled: function() {
		this.setIsEnabled(!this._item.disabled)
	},

	destroy() {
		m_helper.removeNode(this._getIdentifier());
		m_helper.show(this._item);
		
		var index = window.multiselects._items.indexOf(this._item);
		if (index > -1) {
			window.multiselects._items.splice(index, 1);
			window.multiselects.splice(index, 1);
		}
	},

	select: function (val) {
		this._toggle(val, true);
	},
	
	deselect: function(val) {
		this._toggle(val, false);
	},

	setIsEnabled(isEnabled) {
		if (this._isEnabled === isEnabled) return;

		var wrapperItem = document.getElementById(this._getIdentifier());
		if (isEnabled) {
			wrapperItem.classList.remove('disabled');
		} else {
			wrapperItem.classList.add('disabled');
		}
		m_helper.setDisabled(this._item, !isEnabled);
		m_helper.setDisabled(document.getElementById(this._getInputFieldIdentifier()), !isEnabled);

		this._isEnabled = isEnabled;
	},
	
	_toggle: function(val, setCheck) {
		var self = this;
		if (val) {
			m_helper.each(document.getElementById(this._getIdentifier()).querySelectorAll('.multiselect-checkbox'),
				function(e) {
					if (e.dataset.val == val) {
						if (setCheck && !e.checked) {
							m_helper.check(e);
							self._onCheckBoxChange(e, self);
						} else if (!setCheck && e.checked) {
							m_helper.uncheck(e);
							self._onCheckBoxChange(e, self);
						}
					}
				});
				
			self._updateText(self);
		}		
	},


	_checkboxClickEvents: {},
	setCheckBoxClick(id, handler) {
		if (typeof handler === "function") {
			this._checkboxClickEvents[id] = handler;
		} else {
			console.error("Checkbox click handler for checkbox value=" + id + " is not a function");
		}

		return this;
	},

	//append required events
	_appendEvents: function () {
		var self = this;
		document.getElementById(self._getInputFieldIdentifier()).addEventListener('focus', function (event) {
			self._showList(self);
			document.getElementById(self._getInputFieldIdentifier()).value = '';
			m_helper.each(window.multiselects, function(e) {
				if (document.getElementById(e._getItemListIdentifier()).offsetParent &&
					m_helper.parent(event.target, e._getIdentifier())) {
					e._hideList(self);
				}
			});
		});

		document.getElementById(self._getInputFieldIdentifier()).addEventListener('click', function (event) {
            hideMultiselects(event);
			self._showList(self);
			document.getElementById(self._getInputFieldIdentifier()).value = '';
		});

		document.getElementById(self._getIdentifier()).addEventListener('click', function (event) {
			event = event || window.event;
			var target = event.target || event.srcElement;
			if (m_helper.parent(target, self._getIdentifier())) {
				event.stopPropagation();				
			}
		});

		document.getElementById(self._getItemListIdentifier()).addEventListener('mouseover', function () {
			self._showList(self);
		});
		
		m_helper.each(document.getElementById(self._getIdentifier()).querySelectorAll('.multiselect-checkbox'),
			function(e) {
				e.addEventListener('change', function(event) {
					self._onCheckBoxChange(e, self, event);
				});
			});
	},

	_onCheckBoxChange: function (checkbox, self, event) {
			var checkedState = self._performSelectItem(checkbox, self);
			if (typeof self._checkboxClickEvents[checkedState.id] === "function") {
				self._checkboxClickEvents[checkedState.id](checkbox, checkedState);
			}

		self._forceUpdate();
	},
	
	_performSelectItem : function(checkbox, self) {
		var item = JSON.parse(checkbox.dataset.multiselectElement);
		if (checkbox.checked) {
			self._itemCounter++;
			m_helper.select(this._item.options[item.index]);
			m_helper.setActive(checkbox.parentElement.parentElement);

			return { id: item.id, checked: true };
		}

		self._itemCounter--;
		m_helper.deselect(this._item.options[item.index]);
		m_helper.setUnactive(checkbox.parentElement.parentElement);

		return { id: item.id, checked: false };
	},
	
	
	
	_hideList: function (context, event) {
		m_helper.setUnactive(document.getElementById(context._getItemListIdentifier()));
		
		m_helper.showAll(document.getElementById(context._getItemListIdentifier()).querySelectorAll('li'));

		context._updateText(context);

		if (event)
			event.stopPropagation();
	},
	
	_updateText : function(context) {
		var activeItems = document.getElementById(context._getItemListIdentifier()).querySelectorAll('ul .multiselect-active');
		if (activeItems.length > 0) {
			var val = '';
			for (var i = 0; i < (activeItems.length < 5 ? activeItems.length : 5) ; i++) {
				val += activeItems[i].innerText + ", ";
			}

			val = val.substr(0, val.length - 2);

			if (val.length > 20) {
				val = val.substr(0, 17) + '...';
			}
		}
		
		if (activeItems.length == document.getElementById(context._getItemListIdentifier()).querySelectorAll('ul li').length) {
			val = 'Všichni';
		}
		document.getElementById(context._getInputFieldIdentifier()).value = val ? val : '';		
	},

	_showList: function (context) {
		m_helper.setActive(document.getElementById(context._getItemListIdentifier()));
	},

	//updates counter
	_forceUpdate: function () {
		var badge = document.getElementById(this._getInputBadgeIdentifier());
		badge.style.visibility = 'hidden';

		if (this._itemCounter > 1) {
			badge.innerHTML = this._itemCounter;
			badge.style.visibility = 'visible';
			
			var ddArrow = badge.nextElementSibling;
			
			if (this._itemCounter < 10) {
				badge.style.left = '-45px';
				ddArrow.style.marginLeft = '-42px';
			}
			else if (this._itemCounter < 100) {
				badge.style.left = '-50px';
				ddArrow.style.marginLeft = '-47px';
			}
			else if (this._itemCounter < 1000) {
				badge.style.left = '-55px';
				ddArrow.style.marginLeft = '-52px';
			}
			else if (this._itemCounter < 10000) {
				badge.style.left = '-60px';
				ddArrow.style.marginLeft = '-57px';
			}
		}
	},

	//internal representation of combo box items
	_items: undefined,

	//selected items counter
	_itemCounter: 0,

	//flag to set enable/disable of multiselect
	_isEnabled: true,

	//returns all items as js objects
	_getItems: function () {
		if (this._items == undefined) {
			var result = [];
			var opts = this._item.options;
			
			for	(var i = 0; i < opts.length; i++) {
				var insertItem = {
					id: opts[i].value,
					index : i,
					text: opts[i].innerHTML,
					selected: !!opts[i].selected,
					selectElement: opts[i]
				};

				result.push(insertItem);
			}

			this._items = result;
		}

		return this._items;
	},

	//returns unique initial control identifier
	_getItemUniqueIdentifier: function () {
		var id = this._item.getAttribute('id'),
			name = this._item.getAttribute('name');

		if (!(id || name)) {
			throw "Multiselect: object does not contain any identifier (id or name)";
		}

		return id ? id : name;
	},

	//returns unique wrapper identifier
	_getIdentifier: function () {
		return this._getItemUniqueIdentifier() + '_multiSelect';
	},

	//returns unique input field identifier
	_getInputFieldIdentifier: function () {
		return this._getItemUniqueIdentifier() + '_input';
	},

	//returns unique item list identifier
	_getItemListIdentifier: function () {
		return this._getItemUniqueIdentifier() + '_itemList';
	},

	//returns unique counter (badge) identifier
	_getInputBadgeIdentifier: function () {
		return this._getItemUniqueIdentifier() + '_inputCount';
	}
}

//multiselect collection
window.multiselects = [];

if (typeof ($) != 'undefined') {
	//jQuery extension for multiselect
	$.fn.multiselect = function () {
		//if no elements passed - it is not an error
		var res = [];
		if (!window.multiselects._items) {
			window.multiselects._items = [];
		}

		if (this.length != 0) {
			$(this).each(function (i, e) {
                var index = window.multiselects._items.indexOf(e);
                //console.log(e);
				if (index == -1) {
					var inputItem = new Multiselect(e);
					window.multiselects.push(inputItem);
					window.multiselects._items.push(e);

					res.push(inputItem)
				} else {
					res.push(window.multiselects[index]);
				}
			});
		}

		return res.length == 1 ? res[0] : $(res);
	};
	
	$(document).click(function (event) {
		hideMultiselects(event);
	});
} else {
	document.multiselect = function(selector) {
		var res = [];
		if (!window.multiselects._items) {
			window.multiselects._items = [];
		}
		
		m_helper.each(document.querySelectorAll(selector), function(e) {
			var index = window.multiselects._items.indexOf(e);
			if (index == -1) {
				var inputItem = new Multiselect(e);
				window.multiselects.push(inputItem);
				window.multiselects._items.push(e);

				res.push(inputItem)
			} else {
				res.push(window.multiselects[index]);
			}
		});
		
		return res.length == 1 ? res[0] : res;
	}
	
	window.onclick = function(event) {
		hideMultiselects(event);
	};
}

function hideMultiselects(event) {
	m_helper.each(window.multiselects, function(e) {
		if (document.getElementById(e._getItemListIdentifier()).offsetParent &&
				!m_helper.parent(event.target, e._getIdentifier())) {
			e._hideList(e, event);
		}
	});
}

function removeAllMultiselects() {
	m_helper.each(window.multiselects, (e) => {
		m_helper.removeNode(e._getIdentifier());
		m_helper.show(e._item);
	});
	window.multiselects = [];
}