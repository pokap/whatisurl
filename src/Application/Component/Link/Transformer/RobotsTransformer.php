<?php

namespace Application\Component\Link\Transformer;

use Application\Component\Link\Domain\RobotsInterface;
use PhpCollection\Sequence;
use Roboxt\Directive\Directive;
use Roboxt\File;
use Roboxt\UserAgent;

/**
 * Robots transformer service.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RobotsTransformer implements RobotsTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(RobotsInterface $robots, UserAgent $userAgent = null)
    {
        $directives = [];

        if (null !== $userAgent) {
            /** @var \Roboxt\Directive\Directive $directive */
            foreach ($userAgent->allDirectives() as $directive) {
                $directives[$directive->getName()] = $directive->getValue();
            }

            $robots->setUserAgent($userAgent->getName());
        } else {
            $robots->setUserAgent(null);
        }

        $robots->setDirectives($directives);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform(File $file, RobotsInterface $robots)
    {
        $sequence = new Sequence();
        foreach ($robots->getDirectives() as $name => $value) {
            $sequence->add(new Directive($name, $value));
        }

        $userAgent = new UserAgent($robots->getUserAgent(), $sequence);

        $file->addUserAgent($userAgent);
    }
}
