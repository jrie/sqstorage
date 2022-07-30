{literal}
let dropDowns = document.getElementsByTagName('select')

function toggleDropdown (input, container) {
  container.classList.remove('hide')
  container.classList.add('show')
}

function forceMouseOut (input, itemContainer) {
  itemContainer.classList.remove('show')
  itemContainer.classList.add('hide')
  input.blur()
}

function setSelectValue (evt) {
  let target = null
  if (evt.target.dataset['targetid'].startsWith('#', 0)) {
    target = document.querySelector(evt.target.dataset['targetid'])
  } else {
    target = document.querySelector('select[data-targetindex="' + evt.target.dataset['targetindex'] + '"]')
  }

  target.selectedIndex = parseInt(evt.target.dataset['idx'])
  for (let spanChild of target.parentNode.children[0].children[2].children) spanChild.removeAttribute('selected')
  target.parentNode.children[0].children[2].children[target.selectedIndex].setAttribute('selected', 'selected')
  target.dispatchEvent(new window.Event('change'))

  if (target.dataset['nosettitle'] === undefined) {
    for (let child of evt.target.parentNode.parentNode.children) {
      if (child.nodeName === 'INPUT') child.value = target.options[target.selectedIndex].innerText
    }
  }
}

function isInTargetNode (startNode, targetNode, limit) {
  if (limit-- === 0) return false
  if (startNode === null) return false
  if (startNode === targetNode) return true
  return isInTargetNode(startNode.parentNode, targetNode, limit)
}

function searchValue (input, itemContainer) {
  let searchValue = input.value.trim().toLowerCase()
  if (searchValue.length === 0) {
    for (let child of itemContainer.children) {
      child.classList.remove('hidden')
      child.classList.remove('nodisplay')
    }

    return
  }

  let count = 0
  for (let child of itemContainer.children) {
    if (child.getAttribute('value').toLowerCase().indexOf(searchValue) !== -1 || child.textContent.toLowerCase().indexOf(searchValue) !== -1) {
      child.classList.remove('hidden')
      child.classList.remove('nodisplay')
      count++
    } else {
      child.classList.add('hidden')
      child.classList.add('nodisplay')
    }
  }

  if (count === 0) {
    for (let child of itemContainer.children) {
      child.classList.remove('hidden')
      child.classList.remove('nodisplay')
    }
  }
}

let targetIndex = 0
let zIndex = 99999
for (let item of dropDowns) {
  --zIndex

  let container = document.createElement('div')

  container.classList.add('btn')
  container.classList.add('btn-secondary')
  container.classList.add('dropDown')
  container.classList.add('input-group-text')
  container.style.zIndex = zIndex

  item.dataset['targetindex'] = targetIndex;

  let itemContainer = document.createElement('div')
  itemContainer.classList.add('dropDownList')
  itemContainer.classList.add('hide')

  let multiple = item.getAttribute('multiple')
  if (multiple !== null) {
    container.classList.add('multipleSelect')
    if (item.getAttribute('name') !== null) item.setAttribute('name', item.getAttribute('name').replace(/[\[\]]/g, '') + '[]')
  }

  let optionIndex = 0
  for (let option of item.children) {
    let dropDownItem = document.createElement('span')
    for (let attribute of option.getAttributeNames()) dropDownItem.setAttribute(attribute, option.getAttribute(attribute))

    if (multiple !== null) {
      let label = document.createElement('label')
      let checkbox = document.createElement('input')
      checkbox.setAttribute('type', 'checkbox')
      checkbox.setAttribute('tabindex', '-1')
      label.appendChild(checkbox)
      label.appendChild(document.createTextNode(option.innerText))

      if (option.value === '-1') {
        if (option.getAttribute('selected') !== null) {
          checkbox.checked = true
          option.parentNode.parentNode.setAttribute('selected', 'selected')
        }

        checkbox.addEventListener('click', function (evt) {
          let checkboxes = document.querySelectorAll('input[type="checkbox"]')
          let subTarget = evt.target.parentNode.parentNode
          let target = null
          let selected = []

          for (let fieldItem of checkboxes) {
            if (fieldItem.parentNode.parentNode.parentNode === evt.target.parentNode.parentNode.parentNode) {
              if (fieldItem.parentNode.parentNode.getAttribute('value') === '-1') {
                fieldItem.checked = true
                fieldItem.parentNode.parentNode.setAttribute('selected', 'selected')
                selected.push(0)
              } else {
                fieldItem.parentNode.parentNode.removeAttribute('selected')
                fieldItem.checked = false
              }
            }
          }

          if (subTarget.dataset['targetid'].startsWith('#', 0)) {
            target = document.querySelector(subTarget.dataset['targetid'])
          } else {
            target = document.querySelector('select[data-targetindex="' + subTarget.dataset['targetindex'] + '"]')
          }

          for (let option of target.options) {
            option.removeAttribute('selected')
          }
          
          for (let index of selected) {
            target.options[index].setAttribute('selected', 'selected')
          }

          target.dispatchEvent(new window.Event('change'))
          evt.target.parentNode.parentNode.parentNode.scrollTo(0,0)
        })
      } else {
        if (option.getAttribute('selected') !== null) {
          checkbox.checked = true
          option.parentNode.parentNode.setAttribute('selected', 'selected')
        }

        checkbox.addEventListener('click', function (evt) {
          let checkboxes = document.querySelectorAll('input[type="checkbox"]')
          for (let fieldItem of checkboxes) {
            if (fieldItem.parentNode.parentNode.parentNode === evt.target.parentNode.parentNode.parentNode) {
              if (fieldItem.parentNode.parentNode.getAttribute('value') === '-1') {
                fieldItem.checked = false
                fieldItem.parentNode.parentNode.removeAttribute('selected')
              }
            }
          }

          let subTarget = evt.target.parentNode.parentNode
          let selected = []
          let options = []

          for (let fieldItem of checkboxes) {
            if (fieldItem.parentNode.parentNode.parentNode === evt.target.parentNode.parentNode.parentNode) {
              fieldItem.parentNode.parentNode.removeAttribute('selected')
              if (fieldItem.parentNode.parentNode.getAttribute('value') === '-1') {
                fieldItem.checked = false
              } else if (fieldItem.checked === true) {
                fieldItem.parentNode.parentNode.setAttribute('selected', 'selected')
                selected.push(parseInt(fieldItem.parentNode.parentNode.dataset['idx']))
              }
              options.push(fieldItem)
            }
          }

          let target = null
          if (subTarget.dataset['targetid'].startsWith('#', 0)) {
            target = document.querySelector(subTarget.dataset['targetid'])
          } else {
            target = document.querySelector('select[data-targetindex="' + subTarget.dataset['targetindex'] + '"]')
          }

          for (let option of target.options) {
            option.removeAttribute('selected')
          }

          for (let index of selected) {
            target.options[index].setAttribute('selected', 'selected')
          }

          if (selected.length === 0) {
            options[0].checked = true
            options[0].parentNode.parentNode.setAttribute('selected', 'selected')
          }

          target.dispatchEvent(new window.Event('change'))
          target.focus()
        })
      }

      label.addEventListener('keypress', function (evt) {
        if (evt.key === 'Enter' || evt.key === ' ') {
          evt.preventDefault()
          evt.target.click()
          evt.target.focus()
        }
      })

      label.setAttribute('tabindex', '0')
      label.addEventListener('keyup', function (evt) {
        if (evt.key === 'Tab') {
          if (evt.shiftKey && evt.target.parentNode.nextSibling === null) {
            toggleDropdown(input, itemContainer)
          }
        }
      })

      dropDownItem.setAttribute('tabindex', '-1')
      dropDownItem.appendChild(label)

      dropDownItem.addEventListener('keydown', function (evt) {
        if (evt.key === 'ArrowUp' && evt.target.parentNode.previousSibling !== null) {
          evt.preventDefault()
          evt.target.parentNode.previousSibling.children[0].focus()
          evt.target.parentNode.parentNode.scrollBy(0, -evt.target.parentNode.clientHeight)
        } else if (evt.key === 'ArrowDown' && evt.target.parentNode.nextSibling !== null) {
          evt.preventDefault()
          evt.target.parentNode.nextSibling.children[0].focus()
        } else if (evt.key === 'Tab') {
          if (!evt.shiftKey && evt.target.parentNode.nextSibling === null) {
            forceMouseOut(input, itemContainer)
          }
        }
      })
    } else {
      dropDownItem.appendChild(document.createTextNode(option.innerText))

      dropDownItem.setAttribute('tabindex', '0')
      dropDownItem.addEventListener('keydown', function (evt) {
        if (evt.key === 'ArrowUp' && evt.target.previousSibling !== null) {
          evt.target.previousSibling.focus()
        } else if (evt.key === 'ArrowDown' && evt.target.nextSibling !== null) {
          evt.target.nextSibling.focus()
        }
      })

      dropDownItem.addEventListener('keyup', function (evt) {
        if (evt.key === 'Enter' || evt.key === ' ') {
          evt.stopPropagation()
          evt.target.click()
        }
      })
    }

    dropDownItem.addEventListener('focus', function () {
      toggleDropdown(input, itemContainer)
    })

    dropDownItem.addEventListener('blur', function () {
      forceMouseOut(input, itemContainer)
    })

    dropDownItem.addEventListener('click', function (evt) {
      evt.stopPropagation()
      toggleDropdown(input, itemContainer)
    })

    itemContainer.appendChild(dropDownItem)
    dropDownItem.dataset['idx'] = optionIndex++
    dropDownItem.dataset['targetindex'] = targetIndex
    if (item.getAttribute('id') !== null) {
      dropDownItem.dataset['targetid'] = '#' + item.getAttribute('id')
    } else if (item.className !== '') {
      dropDownItem.dataset['targetid'] = targetIndex
    }

    if (multiple === null) dropDownItem.addEventListener('click', setSelectValue)
  }

  let icon = document.createElement('span')
  icon.classList.add('fas')
  // icon.classList.add('fa-caret-down')
  icon.classList.add('fa-align-justify')

  let input = document.createElement('input')
  for (let attribute of item.getAttributeNames()) {
    if (attribute === 'name') {
      input.setAttribute(attribute, item.getAttribute(attribute).toString().replace(/[\[\]]/g, '') + '_input')
      continue
    }
    input.setAttribute(attribute, item.getAttribute(attribute))
  }
  input.className = ''
  input.removeAttribute('id')
  input.removeAttribute('required')
  input.removeAttribute('type')

  input.classList.remove('dropdown-toggle')
  input.classList.remove('dropdown')

  if (item.selectedOptions.length !== 0) {
    if (item.selectedOptions.length !== 0) input.setAttribute('placeholder', item.selectedOptions[0].innerText)
    else input.setAttribute('placeholder', item.options[0].innerText)
    input.value = ''
  } else {
    input.value = ''
    input.setAttribute('placeholder', item.options[0].innerText)
  }

  itemContainer.setAttribute('tabindex', '-1')
  input.setAttribute('tabindex', '0')

  //input.addEventListener('blur', function () { forceMouseOut(input, itemContainer) }) // TODO: Readd on new Opera and Firefox versions
  input.addEventListener('focus', function () {
    toggleDropdown(input, itemContainer)
  })

  input.addEventListener('keyup', function (evt) {
    searchValue(input, itemContainer)
  })
  
  icon.addEventListener('click', function () {
    toggleDropdown(input, itemContainer)
  })

  input.addEventListener('blur', function (evt) {
    if (!isInTargetNode(evt.relatedTarget, container, 8)) forceMouseOut(input, itemContainer)
  })

  input.addEventListener('mouseout', function (evt) {
    if (!isInTargetNode(evt.relatedTarget, container, 8)) forceMouseOut(input, itemContainer)
  })

  itemContainer.addEventListener('mouseout', function (evt) {
    if (!isInTargetNode(evt.relatedTarget, container, 8)) forceMouseOut(input, itemContainer)
  })


  icon.addEventListener('mouseout', function (evt) {
    if (!isInTargetNode(evt.relatedTarget, container, 8)) forceMouseOut(input, itemContainer)
  })

  container.appendChild(icon)
  container.appendChild(input)
  container.appendChild(itemContainer)

  item.parentNode.prepend(container)
  item.classList.add('hidden')
  item.classList.add('nodisplay')
  targetIndex++
}
{/literal}
