require "opentelemetry/sdk"

class DiceController < ApplicationController

    MyAppTracer.in_span("/rolldice") do |span|
        span.set_attribute("HTTP_METHOD", "GET")
        span.add_event("just rolling dice")

        def roll
            render json: (rand(6) + 1).to_s
        end
    end
    
end
