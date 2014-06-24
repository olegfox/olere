var RTOOLBAR = {
	format: 
	{
		name: 'format', title: RLANG.format, func: 'show',
		dropdown: 
		{
			bold: 		  {exec: 'bold', name: 'bold', title: RLANG.bold, style: 'font-weight: bold;'},
			italic: 	  {exec: 'italic', name: 'italic', title: RLANG.italic, style: 'font-style: italic;'},
			superscript:  {exec: 'superscript', name: 'superscript', title: RLANG.superscript},
			strikethrough:  {exec: 'StrikeThrough', name: 'StrikeThrough', title: RLANG.strikethrough, style: 'text-decoration: line-through !important;'},
			removeformat: {exec: 'removeformat', name: 'removeformat', title: RLANG.removeformat}
		}						
	},
}