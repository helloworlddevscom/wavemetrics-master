<?php

namespace Drupal\quote\Plugin\Filter;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Component\Utility\Unicode;

/**
 * Provides a filter for markdown.
 *
 * @Filter(
 *   id = "filter_quote",
 *   module = "quote",
 *   title = @Translation("Quote Filter"),
 *   description = @Translation("Converts [quote] tags into &lt;div&gt; tags. Must usually apply after HTML filters unless an exception is made for &lt;div&gt; tags."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 * )
 */
class QuoteFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    return new FilterProcessResult(_quote_filter_process($text));
  }

  /**
   * Get the tips for the filter.
   *
   * @param bool $long
   *   If get the long or short tip.
   *
   * @return string
   *   The tip to show for the user.
   */
  public function tips($long = FALSE) {
    if ($long) {
      // These string are wrapped in <pre> tags.
      $simple_quote = '[quote]This is a simple quote.[/quote]';
      $attributed_quote = '[quote=Mr. Drupal]This is a quote with an attribution line.[/quote]';
      $nested_quote = '[quote]I think she says it best...
[quote=Ms. Quotation]This is a quote nested within another quote.[/quote]
but you can\'t argue with
[quote=Ms. Reply]The more quotes, the merrier.
Just don\'t get too carried away.[/quote]
And I have nothing more to say.[/quote]';
      return t('<p>Quoted content can be placed between [quote] tags in order to
        be displayed as an indented quote. Every [quote] tag <em>must</em> have a
        corresponding [/quote] tag. For example:
        <pre>!simple-quote</pre> is displayed as:</p>
        !simple-quote-processed
        <p>Additionally, there is an optional attribute which allows quotes to
        specify the original author.
        <pre>!attributed-quote</pre> is displayed as:</p>
        !attributed-quote-processed
        <p>Finally, multiple [quote] tags can be nested within one another. Just
        remember that every [quote] tag <strong>must</strong> have a corresponding
        [/quote] tag.
        <pre>!nested-quote</pre> is displayed as:</p>
        !nested-quote-processed', array(
          '!simple-quote' => $simple_quote,
          '!simple-quote-processed' => _quote_filter_process($simple_quote),
          '!attributed-quote' => $attributed_quote,
          '!attributed-quote-processed' => _quote_filter_process($attributed_quote),
          '!nested-quote' => $nested_quote,
          '!nested-quote-processed' => _quote_filter_process($nested_quote)
        )
      );
    }
    else {
      return t('You may quote other posts using [quote] tags.');
    }
  }
}

/**
 * Replace [quote] tags with markup for display.
 *
 * @param $text
 *   The text with the [quote] tags that need to be replaced with HTML tags.
 *
 * @return $text
 *   Filtered text.
 */
function _quote_filter_process($text) {
  if (stristr($text, '[quote')) {
    // Single regexp with callback allowing for theme calls and quote
    // nesting/recursion with regexp code from
    // http://de.php.net/manual/en/function.preg-replace-callback.php#85836
    $text = preg_replace_callback(
      '#\[(quote.*?)]((?>\[(?!/?quote[^[]*?])|[^[]+|(?R))*)\[/quote]#is',
      function ($matches) {
        static $index = 0;
        $nest = ++$index;
        if (!stristr($matches[2], '[quote')) {
          $index = 0;
        }
        $data_array = array(
          '#theme' => 'quote',
          '#quote_content' => _quote_filter_process($matches[2]),
          '#quote_author' => trim(Unicode::substr($matches[1], 6)),
          '#nest' => $nest,
        );
        $rendered = \Drupal::service('renderer')->render($data_array);
        return $rendered;
      },
      $text
    );
  }
  return $text;
}
